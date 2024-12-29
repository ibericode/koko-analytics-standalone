<?php

namespace App\Controller;

use App\Database;
use App\Repository\StatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends Controller
{
    #[Route('/', name: 'app_dashboard', methods: ['GET'])]
    public function index(Request $request, StatRepository $statsRepository): Response
    {
        try {
            $start = new \DateTimeImmutable($request->query->get('date-start', '-28 days'));
            $end = new \DateTimeImmutable($request->query->get('date-end', 'now'));
        } catch (\Exception $e) {
            $start = new \DateTimeImmutable('-28 days');
            $end = new \DateTimeImmutable('now');
        }

        $date_range = $request->query->get('date-range', '');
        if ($date_range) {
            [$start, $end] = $this->get_dates_from_range($date_range);
        }

        $prev = $start->sub($start->diff($end));
        $totals = $statsRepository->getTotalsBetween($start, $end);
        $totals_previous = $statsRepository->getTotalsBetween($prev, $start);
        $chart = $statsRepository->getGroupedTotalsBetween($start, $end);
        $pages = $statsRepository->getPageStatsBetween($start, $end);
        $referrers = $statsRepository->getReferrerStatsBetween($start, $end);
        return $this->render("dashboard.html.php", [
            'date_start' => $start,
            'date_end' => $end,
            'totals' => $totals,
            'totals_previous' => $totals_previous,
            'chart' => $chart,
            'pages' => $pages,
            'referrers' => $referrers,
            'date_range' => $date_range,
            'date_ranges' => $this->get_date_ranges(),
        ]);
    }

    private function get_dates_from_range(string $range): array {
        $now = new \DateTimeImmutable('now');

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

    private function get_date_ranges(): array {
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
