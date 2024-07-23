<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class DogCatListener
{
    //#[AsEventListener(event: KernelEvents::RESPONSE)]
    public function onKernelResponse(ResponseEvent $event): void
    {
        //Dans la répnose, remplacer les occurrence du 1e mot par le 2e
        $event->getResponse()->setContent('Dog', 'Cat', $event->getResponse()->getContent());
    }
}
