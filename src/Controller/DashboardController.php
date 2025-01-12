<?php

namespace App\Controller;

use App\Aggregator;
use App\Database;
use App\Chart;
use App\Datastore\MysqlStore;
use App\Datastore\StoreInterface;
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

    #[Route('/{domain}', name: 'app_dashboard', methods: ['GET'])]
    public function show(string $domain, Request $request, StatRepository $statsRepository, DomainRepository $domainRepository, Aggregator $aggregator): Response
    {
        $domain = $domainRepository->getByName($domain);
        if (!$domain) {
            $this->createNotFoundException();
        }

        // run the aggregator for this domain whenever the dashboard is loaded
        $aggregator->run($domain);

        $timezone = new \DateTimeZone('UTC');
        try {
            $start = new \DateTimeImmutable($request->query->get('date-start', '-28 days'), $timezone);
            $end = new \DateTimeImmutable($request->query->get('date-end', 'now'), $timezone);
        } catch (\Exception) {
            $start = new \DateTimeImmutable('-28 days', $timezone);
            $end = new \DateTimeImmutable('now', $timezone);
        }

        $date_range = $request->query->get('date-range', 'custom');
        if ($date_range !== 'custom') {
            [$start, $end] = $this->getDatesFromRange($date_range);
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
        ]);
    }

    private function getDatesFromRange(string $range): array
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        // TODO: Make it configurable which day is start of week
        $start_of_week = 0;

        switch ($range) {
            case 'today':
                return [
                    $now->modify('today midnight'),
                    $now->modify('tomorrow midnight, -1 second')
                ];
            case 'yesterday':
                return [
                    $now->modify('yesterday midnight'),
                    $now->modify('today midnight, -1 second')
                ];
            case 'this_week':
                return [
                    ($now->modify('sunday, midnight'))->modify("+$start_of_week days"),
                    ($now->modify('next sunday, midnight, -1 second'))->modify("+$start_of_week days")
                ];
            case 'last_week':
                return [
                    ($now->modify('sunday, midnight, -7 days'))->modify("+$start_of_week days"),
                    ($now->modify('sunday, midnight, -1 second'))->modify("+$start_of_week days"),
                ];
            default:
            case 'this_month':
                return [
                    $now->modify('first day of this month'),
                    $now->modify('last day of this month')
                ];
            case 'last_month':
                return [
                    $now->modify('first day of last month, midnight'),
                    $now->modify('last day of last month')
                ];
            case 'this_year':
                return [
                    $now->setDate($now->format('Y'), 1, 1),
                    $now->setDate($now->format('Y'), 12, 31),
                ];
            case 'last_year':
                return [
                    $now->setDate($now->format('Y') - 1, 1, 1),
                    $now->setDate($now->format('Y') - 1, 12, 31),
                ];
        }
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
}
