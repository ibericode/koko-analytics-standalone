<?php

// src/EventListener/RequestListener.php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class RequestListener
{
    #[AsEventListener]
    public function onKernelRequest(RequestEvent $event): void
    {
        // don't do anything if it's not the main request
        if (!$event->isMainRequest()) {
            return;
        }

        // don't do anything if request is not for a protected URL path
        $request = $event->getRequest();
        if ($request->getRequestUri() !== '/') {
            return;
        }



        dd('here');


        // ...
    }
}
