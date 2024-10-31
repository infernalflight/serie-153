<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'serie:launch',
    description: 'Add a short description for your command',
)]
class SerieCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $response = $io->ask('Bonjour, comment allez vous ?', 'Bien!');

        $confirm = $io->confirm('Etes-vous sûr ?', false);

        $io->choice('Quel parfum pour la glace ?', ['Vanille', 'Fraise', 'Pistache']);

        $io->error("Y'en a plus");

        $io->writeln('Ah si y en a !!!');


        $io->success('Tout s\'est bien passé !');

        return Command::SUCCESS;
    }
}
