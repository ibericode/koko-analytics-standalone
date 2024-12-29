<?php

namespace App\EventListener;

use App\Repository\UserRepository;
use App\Security\User;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class RequestListener
{
    public function __construct(protected UserRepository $userRepository) {}

    #[AsEventListener]
    public function onKernelRequest(RequestEvent $event): void
    {
        // don't do anything if it's not the main request
        if (!$event->isMainRequest()) {
            return;
        }

        // don't do anything if request is not for a protected URL path
        $request = $event->getRequest();
        if ($request->getRequestUri() === '/login') {
            return;
        }

        // get user from session
        $session = $request->getSession();
        $user = $session->get('user');
        if (!$user instanceof User) {
            $event->setResponse(new RedirectResponse('/login'));
            return;
        }

        // abort session if user credentials changed
        $user2 = $this->userRepository->getByEmail($user->getEmail());
        if ($user->getEmail() !== $user2->getEmail() || $user->getPassword() !== $user2->getPassword()) {
            $session->remove('user');
            $session->invalidate();
            $event->setResponse(new RedirectResponse('/login'));
            return;
        }

        // user is authenticated!

        // TODO: check for proper role for certain parts (ie admin role for settings)
    }
}
