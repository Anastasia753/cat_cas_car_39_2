<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Mailer
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendWelcomeMail(User $user)
    {
        $this->send("email/welcome.html.twig", 'Добро пожаловать на CatCasCar', $user);
    }

    public function sendWeeklyNewsletter(User $user, array $articles)
    {
        $this->send(
            'email/weekly-newsletter.html.twig',
            'Еженедельная рассылка статей Cat-Cas-Car',
            $user,
            function (TemplatedEmail $email) use ($articles) {
                $email
                    ->context([
                        'articles' => $articles,
                    ])
                    ->attach('Опублтклвано статей на сайте: ' . count($articles), 'report_' . date('Y-m-d') . '.txt')
                ;
            }
        );
    }

    private function send(string $template, string $subject, User $user, \Closure $callback = null)
    {
        $email = (new TemplatedEmail())
            ->from(new Address('kipriyanova753@gmail.com', 'Cat-Cas-Car'))
            ->to(new Address($user->getEmail(), $user->getFirstName()))
            ->subject($subject)
            ->htmlTemplate($template)
        ;

        if ($callback) {
            $callback($email);
        }

        $this->mailer->send($email);
    }
}