<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Security\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AuthController extends Controller
{
    #[Route('/login', name: "app_login")]
    public function login(Request $request, UserRepository $userRepository): Response
    {
        // check if form submitted
        if ($request->getMethod() === Request::METHOD_POST) {
            $identifier = $request->request->getString('_username', '');
            $password = $request->request->getString('_password', '');
            $user = $userRepository->getByEmail($identifier);
            $userPassword = $user ? $user->getPassword() : '';
            if (\password_verify($password, $userPassword) && $user) {
                $session = $request->getSession();
                $session->set('user', $user);
                $session->save();
                return new RedirectResponse('/');
            } else {
                $error = 'Invalid credentials.';
            }
        }

        return $this->render("login.html.php", [
            'last_username' => $identifier ?? '',
            'error' => $error ?? '',
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(Request $request): Response
    {
        $session = $request->getSession();
        $session->invalidate();
        return new RedirectResponse('/login');
    }
}
