<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class ExampleEventListener
{

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onEventHappen(RequestEvent $event): void
    {
        $this->logger->info(sprintf('Был вызван обработчик события %s', __METHOD__));
    }
}