<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\SenderService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;

class NewsletterCommand extends Command
{
    protected static $defaultName = 'app:newsletter';

    private $senderService;
    private $userRepository;

    public function __construct(SenderService $senderService, UserRepository $userRepository)
    {
        $this->senderService = $senderService;
        $this->userRepository = $userRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Sending a newsletter to users');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $users = $this->userRepository->findBy(['newsletter' => User::VALIDATED]);

        $this->senderService->newsletterEmail($users);

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }
}