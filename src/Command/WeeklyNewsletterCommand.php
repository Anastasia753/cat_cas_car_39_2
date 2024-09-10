<?php

namespace App\Command;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use App\Service\Mailer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mime\Address;

class WeeklyNewsletterCommand extends Command
{
    protected static $defaultName = 'app:weekly-newsletter';
    private UserRepository $userRepository;
    private ArticleRepository $articleRepository;
    private Mailer $mailer;

    public function __construct(UserRepository $userRepository, ArticleRepository $articleRepository, Mailer $mailer)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->articleRepository = $articleRepository;
        $this->mailer = $mailer;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Еженедельная рассылка статей')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var User[] $users */
       $users = $this->userRepository->findAllSubscribedUsers();

        /** @var Article[] $articles */
       $articles = $this->articleRepository->findAllPublishedLastWeek();

       $io = new SymfonyStyle($input, $output);

       if (count($articles) == 0){
           $io->warning('За последнюю неделю никто не публиковал статьи');
           return  0;
       }

       $io->progressStart(count($users));

       foreach ($users as $user) {
           $this->mailer->sendWeeklyNewsletter($user, $articles);

           $io->progressAdvance();

           break;
       }

       $io->progressFinish();

       return 0;
    }
}
