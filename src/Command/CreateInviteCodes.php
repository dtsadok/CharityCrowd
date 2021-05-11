<?php
namespace App\Command;

use App\Service\InviteCodeGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateInviteCodes extends Command
{
    protected static $defaultName = 'app:create-invite-codes';

    private $entityManager;
    private $inviteCodeGenerator;

    public function __construct(EntityManagerInterface $entityManager, InviteCodeGenerator $inviteCodeGenerator)
    {
        $this->entityManager = $entityManager;
        $this->inviteCodeGenerator = $inviteCodeGenerator;

        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = 25;
        $codeLength = 6;
        $this->inviteCodeGenerator->makeInviteCodes($count, $codeLength, $this->entityManager);

        return Command::SUCCESS;
    }
}
