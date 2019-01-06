<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use App\ApiEntity\Track;
use App\ApiEntity\Tracks;

use GuzzleHttp\Client;

class ImportOldSystemDataCommand extends Command
{
    protected static $defaultName = 'importOldSystemData';

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
        $response = $client->request('GET', 'https://api.playlist.science');
        $responseJson = json_decode($response->getBody());
        $tracks = $this->parseData($responseJson);
        $tags = $tracks->getTags(); // get array of unique tags from list to be stored into db


        $io->success('Import successful.');
    }

    protected function parseData($json)
    {
        $type = gettype($json);
        if ($type === "array") {

            $tracks = new Tracks([]);

            foreach ($json as $trackObject) {

                // match up the data
                $track = new Track([
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
