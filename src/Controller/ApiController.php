<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Track;
use App\Repository\TrackRepository;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/tracks", name="api-tracks")
     */
    public function tracks(TrackRepository $trackRepository)
    {
        $tracks = $trackRepository->fetchAvailableTracks();

        return $this->json($tracks);
    }
}
