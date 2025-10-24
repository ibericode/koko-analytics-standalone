<?php

namespace App\Command;

use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:user:create', description: 'Registers a new user.')]
class UserCreateCommand extends Command
{
    public function __construct(protected UserRepository $userRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The email address of the user.')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the user.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $raw_password = $input->getArgument('password');
        $password = password_hash($raw_password, PASSWORD_DEFAULT);
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($password);
        $this->userRepository->save($user);
        return Command::SUCCESS;
    }
}
