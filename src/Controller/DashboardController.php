<?php

namespace App\Controller;

use App\Database;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class DashboardController extends Controller
{
    #[Route('/', name: 'app_dashboard')]
    public function index(Database $db): Response
    {
        return $this->render("dashboard.html.php", []);
    }
}
