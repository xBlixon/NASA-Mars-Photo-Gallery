<?php

namespace App\Command;

use GuzzleHttp\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:fetch-holidays',
    description: 'Fetches polish holidays from 2021.',
)]
class FetchHolidaysCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
//        https://www.openholidaysapi.org <- Source for the data.
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

//        if ($arg1) {
//            $io->note(sprintf('You passed an argument: %s', $arg1));
//        }
//
//        if ($input->getOption('option1')) {
//            // ...
//        }
//
//        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        $client = new Client();
        $res = $client->request("GET", "https://openholidaysapi.org/PublicHolidays?countryIsoCode=PL&languageIsoCode=PL&validFrom=2021-01-01&validTo=2021-12-31");
        $json = $res->getBody()->getContents();
        var_export($json);
        $json = json_decode($json);
//        $json = var_export($json, true);
//        $io->writeln($json);
        foreach ($json as $index => $item) {
            $io->writeln("\n" . $index . ":");
            $io->writeln($item->id);
            $io->writeln($item->name[0]->text);
        }
//        var_export($json);
        return Command::SUCCESS;
    }
}
