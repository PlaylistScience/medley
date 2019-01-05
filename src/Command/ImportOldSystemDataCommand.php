<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportOldSystemDataCommand extends Command
{
    protected static $defaultName = 'importOldSystemData';

    protected function configure()
    {
        $this
            ->setDescription('Imports data from live playlist.science/songof.today (old system) instance')
            ->addArgument('url', InputArgument::REQUIRED, 'Url location of live playlist.science/songof.today instance');
            // ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $url = $input->getArgument('url');

        // if ($arg1) {
        //     $io->note(sprintf('You passed an argument: %s', $arg1));
        // } else {
        //     $io->note('Argument missing');
        // }

        // if ($input->getOption('option1')) {
        //     // ...
        // }

        $io->success('Import successful.');
    }
}
