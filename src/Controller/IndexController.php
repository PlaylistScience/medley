<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\TrackRepository;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(TrackRepository $trackRepository)
    {
        $tracks = $trackRepository->findAll();

        return $this->render('index.html.twig', [
            'tracks' => $tracks,
            'env' => getenv('ENV'),
        ]);
    }
}
