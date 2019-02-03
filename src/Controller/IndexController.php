<?php

namespace App\Controller;

use App\Repository\TrackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
            'env'    => getenv('ENV'),
        ]);
    }
}
