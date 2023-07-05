<?php

namespace App\Command;

use App\Entity\RoverPhoto;
use App\Repository\HolidayRepository;
use App\Repository\RoverPhotoRepository;
use Doctrine\DBAL\Types\StringType;
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
    name: 'app:fetch-rover-photos',
    description: 'Add a short description for your command',
)]
class FetchRoverPhotosCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private HolidayRepository $holidayRepository,
        private RoverPhotoRepository $roverPhotoRepository
    ){
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $holidays = $this->holidayRepository->findAll();
        $allPhotos = [];

//        foreach (['curiosity', 'opportunity', 'spirit'] as $roverName) //This won't work since only curiosity did photos in 2021
        foreach (['curiosity'] as $roverName)
        {
            foreach ($holidays as $holiday)
            {
                $dates = $this->getAllDatesFromPeriod($holiday->getStartDate(), $holiday->getEndDate());
                foreach ($dates as $date)
                {
                    $json = json_decode($this->getPhotosFromDate($date, $roverName));
                    $allPhotos = array_merge($allPhotos, $this->jsonToRoverPhotosEntities($json));
                    $io->write('.');
                    usleep(500000); //0.5s
                }
            }
        }
        $this->roverPhotoRepository->wipe();
        $this->roverPhotoRepository->saveMany($allPhotos, true);
        $io->success("All photos were fetched.");
        return Command::SUCCESS;
    }

    /** @return string[] */
    private function getAllDatesFromPeriod(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        $end = clone $end;
        $end->modify("+1 day");

        $period = new \DatePeriod(
            $start,
            new \DateInterval('P1D'),
            $end
        );
        $stringDates = [];
        foreach ($period as $date) {
            $stringDates[] = $date->format('Y-m-d');
        }
        return $stringDates;
    }

    private function getPhotosFromDate(string $date, string $roverName): string
    {
        $API_KEY = $_ENV['NASA_API_KEY'];
        $client = new Client();
        $res = $client->get("https://api.nasa.gov/mars-photos/api/v1/rovers/$roverName/photos?earth_date=$date&api_key=$API_KEY");
        return $res->getBody()->getContents();
    }

    /** @return RoverPhoto[] */
    private function jsonToRoverPhotosEntities(object $json): array
    {
        $roverPhotos = [];
        foreach ($json->photos as $photo)
        {
            $roverPhoto = new RoverPhoto();
            $roverPhoto->setId($photo->id)
                ->setRoverName($photo->rover->name)
                ->setCameraName($photo->camera->name)
                ->setEarthDate(new \DateTime($photo->earth_date))
                ->setImageURL($photo->img_src)
            ;
            $roverPhotos[] = $roverPhoto;
        }
        return $roverPhotos;
    }
}
