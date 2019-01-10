<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\GenreRepository;
use App\Entity\Genre;
use App\Entity\Track;
use App\ApiEntity\ApiTrack;
use App\ApiEntity\ApiTracks;
use GuzzleHttp\Client;

class ImportOldSystemDataCommand extends Command
{
    protected static $defaultName = 'importOldSystemData';
    var $em;
    var $genreRepository;

    public function __construct(EntityManagerInterface $em, GenreRepository $genreRepository)
    {
        $this->em = $em;
        $this->genreRepository = $genreRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Imports data from live playlist.science/songof.today (old system) instance')
            ->addArgument('url', InputArgument::REQUIRED, 'Url location of live playlist.science/songof.today instance');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $url = $input->getArgument('url');

        $client = new Client();
        $response = $client->request('GET', $url);
        $responseJson = json_decode($response->getBody());
        $tracks = $this->parseData($responseJson);
        $tags = $tracks->getTags(); // get array of unique tags from list to be stored into db

        foreach ($tags as $tagName) {
            // save $tag to genre repository
            // they are called genres in new system
            $this->createAndSaveGenre($tagName);
        }

        foreach ($tracks as $track) {
            $this->createAndSaveTrack($track);
        }

        $io->success('Import successful.');
    }

    protected function createAndSaveTrack($jsonTrack)
    {
        $apiTrack = new ApiTrack($jsonTrack);
        $track = new Track();

        // Set required values
        $track->setName($apiTrack->getName());
        $track->setUrl($apiTrack->getUrl());
        $track->setCreatedAt($apiTrack->getCreatedAt());
    }

    protected function createAndSaveGenre($name)
    {
        // check if genre with name already exists
        if (!$this->genreRepository->findByName($name)) {
            $genre = new Genre();
            $genre->setName($name);
            $this->em->persist($genre);
            $this->em->flush();
        }
    }

    protected function parseData($json)
    {
        $type = gettype($json);
        if ($type === "array") {
            $tracks = new ApiTracks([]);
            foreach ($json as $trackObject) {
                // match up the data
                $track = new ApiTrack([
                    'title' => $trackObject->title,
                    'url' => $trackObject->url,
                    'createdAt' => $trackObject->createdAt,
                ]);
                // match up optional data
                if (isset($trackObject->tags)) $track->setTags($trackObject->tags);
                $tracks->addTrack($track);
            }
            return $tracks;
        } else return false;
    }
}
