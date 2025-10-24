<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:user:delete', description: 'Deletes a user')]
class UserDeleteCommand extends Command
{
    public function __construct(protected UserRepository $userRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The email address of the user.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');

        $user = $this->userRepository->getByEmail($email);
        if (!$user) {
            $output->writeln("No user with email {$email}");
            return Command::FAILURE;
        }

        $this->userRepository->delete($user);
        return Command::SUCCESS;
    }
}
