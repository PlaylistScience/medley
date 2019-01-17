<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Track;
use App\Repository\TrackRepository;
use App\Repository\SanitizedUserRepository;

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

    /**
     * @Route("/api/users", name="api-users")
     */
    public function users(SanitizedUserRepository $sanitizedUserRepository)
    {
        $users = $sanitizedUserRepository->findAll();

        return $this->json($users);
    }
}
