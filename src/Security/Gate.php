<?php

namespace App\Security;

use App\Repository\UserRepository;
use App\Security\User;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class Gate
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
        $public_access_urls = [
            '/login',
            '/collect'
        ];
        $request = $event->getRequest();
        if (\in_array($request->getPathInfo(), $public_access_urls, true)) {
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
        if (!$user2 || $user->getEmail() !== $user2->getEmail() || $user->getPassword() !== $user2->getPassword()) {
            $session->remove('user');
            $session->invalidate();
            $event->setResponse(new RedirectResponse('/login'));
            return;
        }

        // user is authenticated!

        // TODO: check for proper role for certain parts (ie admin role for settings)
    }
}
