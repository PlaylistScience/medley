<?php

namespace App\Command;

use App\ApiEntity\ApiTrack;
use App\ApiEntity\ApiTracks;
use App\Entity\Genre;
use App\Entity\Track;
use App\Repository\GenreRepository;
use App\Repository\TrackRepository;
use App\Repository\UserRepository;
use DateTime;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportOldSystemDataCommand extends Command
{
    protected static $defaultName = 'importOldSystemData';
    public $em;
    public $genreRepository;

    public function __construct(TrackRepository $trackRepository, GenreRepository $genreRepository, UserRepository $userRepository)
    {
        $this->genreRepository = $genreRepository;
        $this->trackRepository = $trackRepository;
        $this->userRepository = $userRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Imports data from live playlist.science/songof.today (old system) instance')
            ->addArgument('url', InputArgument::REQUIRED, 'Url location of live playlist.science/songof.today instance')
            ->addArgument('userId', InputArgument::REQUIRED, 'User id of song owner');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $url = $input->getArgument('url');
        $userId = $input->getArgument('userId');

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

        foreach ($tracks->tracks as $track) {
            $this->createAndSaveTrack($track, $userId);
        }

        $io->success('Import successful.');
    }

    protected function createAndSaveTrack($apiTrack, $userId)
    {
        // $apiTrack = new ApiTrack($jsonTrack);
        $track = new Track();

        if ($apiTrack->hasTags()) {
            foreach ($apiTrack->getTags() as $apiTag) {
                $genre = $this->genreRepository->findOneByName($apiTag);
                $track->addGenre($genre);
            }
        }

        // Set required values
        $track->setName($apiTrack->getName());
        $track->setArtist(''); // dummy value
        $track->setUrl($apiTrack->getUrl());
        $track->setCreatedAt(new DateTime($apiTrack->getCreatedAt()));

        // Set owner to param passed by id
        $owner = $this->userRepository->findOneById($userId);
        $track->setOwner($owner);

        $this->trackRepository->save($track);
    }

    protected function createAndSaveGenre($name)
    {
        // check if genre with name already exists
        if (!$this->genreRepository->findByName($name)) {
            $genre = new Genre();
            $genre->setName($name);
            $this->genreRepository->save($genre);
        }
    }

    protected function parseData($json)
    {
        $type = gettype($json);
        if ($type === 'array') {
            $tracks = new ApiTracks([]);
            foreach ($json as $trackObject) {
                // match up the data
                $track = new ApiTrack([
                    'name'      => $trackObject->title,
                    'url'       => $trackObject->url,
                    'createdAt' => $trackObject->createdAt,
                ]);
                // match up optional data
                if (isset($trackObject->tags)) {
                    $track->setTags($trackObject->tags);
                }
                $tracks->addTrack($track);
            }

            return $tracks;
        } else {
            return false;
        }
    }
}
