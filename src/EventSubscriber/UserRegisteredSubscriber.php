<?php

namespace App\EventSubscriber;

use App\Events\UserRegisteredEvent;
use App\Service\Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserRegisteredSubscriber implements EventSubscriberInterface
{

    private Mailer $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function onUserRegistered(UserRegisteredEvent $event): void
    {
        $this->mailer->sendWelcomeMail($event->getUser());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserRegisteredEvent::class => 'onUserRegistered',
        ];
    }
}