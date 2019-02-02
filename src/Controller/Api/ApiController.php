<?php

namespace App\Controller\Api;

use App\Repository\TrackRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
    public function users(UserRepository $userRepository)
    {
        $users = $userRepository->sanitizedUsers();

        return $this->json($users);
    }

    /**
     * @Route("/api/user/{id}", name="api-user")
     */
    public function user($id, UserRepository $userRepository)
    {
        $user = $userRepository->sanitizedUser($id);

        return $this->json($user);
    }

    /**
     * @Route("/api/user/{id}/tracks", name="api-user-tracks")
     */
    public function userTracks($id, UserRepository $userRepository, TrackRepository $trackRepository)
    {
        $tracks = $trackRepository->fetchByUserId($id);

        return $this->json($tracks);
    }
}
