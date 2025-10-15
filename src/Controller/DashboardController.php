<?php

namespace App\Controller;

use App\Aggregator;
use App\Database;
use App\Chart;
use App\Datastore\MysqlStore;
use App\Datastore\StoreInterface;
use App\Dates;
use App\Entity\Domain;
use App\Repository\DomainRepository;
use App\Repository\StatRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends Controller
{
    #[Route('/', name: 'app_dashboard_list', methods: ['GET'])]
    public function index(DomainRepository $domainRepository): Response
    {
        $domains = $domainRepository->getAll();

        // if there is only a single domain, redirect directly to it
        if (count($domains) === 1) {
            return $this->redirectToRoute('app_dashboard', ['domain' => $domains[0]->name ]);
        }

        return $this->render('dashboard-list.html.php', [
            'domains' => $domains,
        ]);
    }

    #[Route('/create', name: 'app_dashboard_create', methods: ['GET', 'POST'])]
    public function create(Request $request, DomainRepository $domainRepository, StatRepository $statRepository): Response
    {
        if ($request->getMethod() === Request::METHOD_POST) {
            $domain = new Domain();
            $domain->name = trim($request->request->get('name', ''));

            // validate domain name
            if ($domain->name === '' || !preg_match('/[a-zA-Z0-9\-\.]+/', $domain->name)) {
                return $this->render('dashboard-create.html.php', [ 'error' => 'Domain name can not be empty or contain non-alphanumeric characters.' ]);
            }

            $domainRepository->insert($domain);
            $statRepository->createTables($domain);
            $this->addFlash('success', 'Domain created');
            return $this->redirectToRoute('app_dashboard', ['domain' => $domain->name]);
        }

        return $this->render('dashboard-create.html.php', [ 'error' => '' ]);
    }

    #[Route('/{domain}/delete', name: 'app_dashboard_delete', methods: ['POST'])]
    public function delete(string $domain, DomainRepository $domainRepository, StatRepository $statRepository): Response
    {
        $domain = $domainRepository->getByName($domain);
        if (!$domain) {
            $this->createNotFoundException();
        }
        $statRepository->reset($domain);
        $domainRepository->delete($domain);
        $this->addFlash('danger', 'Domain deleted');
        return $this->redirectToRoute('app_dashboard_list', []);
    }

    #[Route('/{domain}', name: 'app_dashboard', methods: ['GET'])]
    public function show(
        string $domain,
        Request $request,
        StatRepository $statsRepository,
        DomainRepository $domainRepository,
        Aggregator $aggregator
    ): Response {
        $domain = $domainRepository->getByName($domain);
        if (!$domain) {
            $this->createNotFoundException();
        }

        $domains = $domainRepository->getAll();

        // run the aggregator for this domain whenever the dashboard is loaded
        $aggregator->run($domain);

        $timezone = new \DateTimeZone($domain->timezone);
        $now = new \DateTimeImmutable('now', $timezone);
        try {
            $start = new \DateTimeImmutable($request->query->get('date-start', '-28 days'), $timezone);
            $end = new \DateTimeImmutable($request->query->get('date-end', 'now'), $timezone);
        } catch (\Exception) {
            $start = new \DateTimeImmutable('-28 days', $timezone);
            $end = $now;
        }

        $path = $request->query->get('path', '');
        $date_range = $request->query->get('date-range', 'custom');
        if ($date_range !== 'custom') {
            [$start, $end] = (new Dates())->getDateRange($date_range, new DateTimeImmutable('now', $timezone));
        }

        $diff = $start->diff($end);
        $prev_start = $start->sub($diff);
        $prev_end = $prev_start->add($end > $now ? $start->diff($now) : $diff);
        $totals = $statsRepository->getTotalsBetween($domain, $path, $start, $end);
        $totals_previous = $statsRepository->getTotalsBetween($domain, $path, $prev_start, $prev_end);
        $chart = $statsRepository->getGroupedTotalsBetween($domain, $path, $start, $end);
        $pages = $statsRepository->getPageStatsBetween($domain, $start, $end);
        $referrers = $statsRepository->getReferrerStatsBetween($domain, $start, $end);
        $realtime_count = $statsRepository->getRealtimeCount($domain);
        $chart = new Chart($chart, $start, $end);

        return $this->render("dashboard.html.php", [
            'date_start' => $start,
            'date_end' => $end,
            'totals' => $totals,
            'totals_previous' => $totals_previous,
            'chart' => $chart,
            'pages' => $pages,
            'referrers' => $referrers,
            'realtime_count' => $realtime_count,
            'date_range' => $date_range,
            'date_ranges' => $this->getDateRanges(),
            'domain' => $domain,
            'domains' => $domains,
            'url_params' => [
                'domain' => $domain->name,
                'date-start' => $request->query->get('date-start', null),
                'date-end' => $request->query->get('date-end', null),
                'date-range' => $request->query->get('date-range', null),
            ],
            'path' => $path,
        ]);
    }

    private function getDateRanges(): array
    {
        return [
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            'this_week' => 'This week',
            'last_week' => 'Last week',
            'this_month' => 'This month',
            'last_month' => 'Last month',
            'this_year' => 'This year',
            'last_year' => 'Last year',
        ];
    }

    #[Route('/{domain}/settings', name: 'app_dashboard_settings', methods: ['GET', 'POST'])]
    public function settings(string $domain, Request $request, DomainRepository $domainRepository)
    {
        $domain = $domainRepository->getByName($domain);
        if (!$domain) {
            $this->createNotFoundException();
        }

        if ($request->getMethod() == Request::METHOD_POST) {
            $posted = $request->request->all('domain');
            $domain->name = (trim($posted['name'] ?? $domain->name));
            $domain->purge_treshold = (int) $posted['purge_treshold'] ?? $domain->purge_treshold;
            $domain->timezone = trim($posted['timezone'] ?? $domain->timezone);
            $domain->excluded_ip_addresses = array_map('trim', explode("\n", trim($posted['excluded_ip_addresses'])));
            $domainRepository->update($domain);

            // write list of ignored ip address to var/domain-ignore
            // we store this in a file so that the /collect endpoint does not have to initiate a database connection on every request
            $filename = dirname(__DIR__, 2) . "/var/{$domain->name}-ignored-ips.txt";
            file_put_contents($filename, join(PHP_EOL, $domain->excluded_ip_addresses));

            $this->addFlash('info', 'Settings saved');
            return $this->redirectToRoute('app_dashboard_settings', ['domain' => $domain->name]);
        }

        return $this->render('settings.html.php', [
            'domain' => $domain,
        ]);
    }
}
