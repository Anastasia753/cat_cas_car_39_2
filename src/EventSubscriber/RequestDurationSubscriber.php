<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestDurationSubscriber implements EventSubscriberInterface
{
    private $startedAt;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function startTimer(RequestEvent $event): void
    {
        if (! $event->isMasterRequest()) {
            return;
        }

        $this->startedAt = microtime(true);
    }

    public function endTimer(RequestEvent $event): void
    {
        if (! $event->isMasterRequest()) {
            return;
        }

        $this->logger->info(sprintf('Мы насчитали, что запрос выполнялся: %f мс', (microtime(true) - $this->startedAt) * 1000));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => [
                ['startTimer', 4000],
            ],
            RequestEvent::class => 'endTimer',
        ];
    }
}
