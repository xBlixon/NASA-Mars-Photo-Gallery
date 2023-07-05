<?php

namespace App\Command;

use App\Entity\Holiday;
use App\Repository\HolidayRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function __construct(
        private EntityManagerInterface $entityManager,
        private HolidayRepository $holidayRepository
    ){
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->holidayRepository->wipe();
//        https://www.openholidaysapi.org <- Source for the data.
        $io = new SymfonyStyle($input, $output);

        $client = new Client();
        $res = $client->request("GET", "https://openholidaysapi.org/PublicHolidays?countryIsoCode=PL&languageIsoCode=PL&validFrom=2021-01-01&validTo=2021-12-31");
        $json = $res->getBody()->getContents();
        $json = json_decode($json);

        foreach ($json as $item)
        {
            $holiday = new Holiday();
            $holiday->setName($item->name[0]->text)
                ->setNationwide($item->nationwide)
                ->setType($item->type)
                ->setStartDate(new \DateTime($item->startDate))
                ->setEndDate(new \DateTime($item->endDate))
                ;
            $this->entityManager->persist($holiday);
        }
        $this->entityManager->flush();

        $io->success("Holiday list has been updated.");

        return Command::SUCCESS;
    }
}
