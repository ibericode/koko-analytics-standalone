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
    public function index(StatRepository $statsRepository): Response
    {
        $start = new \DateTimeImmutable('-28 days');
        $end = new \DateTimeImmutable('now');
        $totals = $statsRepository->getTotalsBetween($start, $end);
        $pages = $statsRepository->getPageStatsBetween($start, $end);
        $referrers = $statsRepository->getReferrerStatsBetween($start, $end);
        return $this->render("dashboard.html.php", [
            'totals' => $totals,
            'pages' => $pages,
            'referrers' => $referrers,
        ]);
    }
}
