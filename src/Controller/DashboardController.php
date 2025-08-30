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
            return $this->redirectToRoute('app_dashboard', ['domain' => $domains[0]->getName() ]);
        }

        return $this->render('dashboard-list.html.php', [
            'domains' => $domains,
        ]);
    }

    #[Route('/create', methods: ['GET', 'POST'])]
    public function create(Request $request, DomainRepository $domainRepository, StatRepository $statRepository): Response
    {

        if ($request->getMethod() === Request::METHOD_POST) {
            $domain = new Domain();
            $domain->setName($request->request->get('name', ''));
            $domainRepository->insert($domain);
            $statRepository->createTables($domain);
            return $this->redirectToRoute('app_dashboard', ['domain' => $domain->getName()]);
        }

        return $this->render('dashboard-create.html.php', []);
    }

    #[Route('/{domain}/delete', methods: ['POST'])]
    public function delete(string $domain, DomainRepository $domainRepository, StatRepository $statRepository): Response
    {
        $domain = $domainRepository->getByName($domain);
        if (!$domain) {
            $this->createNotFoundException();
        }
        $statRepository->reset($domain);
        $domainRepository->delete($domain);
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

        $settings = $domainRepository->getSettings($domain);

        // run the aggregator for this domain whenever the dashboard is loaded
        $aggregator->run($domain);

        $timezone = new \DateTimeZone($settings['timezone']);
        try {
            $start = new \DateTimeImmutable($request->query->get('date-start', '-28 days'), $timezone);
            $end = new \DateTimeImmutable($request->query->get('date-end', 'now'), $timezone);
        } catch (\Exception) {
            $start = new \DateTimeImmutable('-28 days', $timezone);
            $end = new \DateTimeImmutable('now', $timezone);
        }

        $date_range = $request->query->get('date-range', 'custom');
        if ($date_range !== 'custom') {
            [$start, $end] = (new Dates())->getDateRange($date_range);
        }

        $prev = $start->sub($start->diff($end));
        $totals = $statsRepository->getTotalsBetween($domain, $start, $end);
        $totals_previous = $statsRepository->getTotalsBetween($domain, $prev, $start);
        $chart = $statsRepository->getGroupedTotalsBetween($domain, $start, $end);
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

        $settings = $domainRepository->getSettings($domain);

        if ($request->getMethod() == Request::METHOD_POST) {
            $settings = $request->request->all('settings');
            $domainRepository->saveSettings($domain, $settings);
            return $this->redirectToRoute('app_dashboard_settings', ['domain' => $domain->getName()]);
        }

        return $this->render('settings.html.php', [
            'domain' => $domain,
            'settings' => $settings,
        ]);
    }
}
