<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\SongRepository;
use App\Repository\UserRepository;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(SongRepository $songRepository, UserRepository $userRepository)
    {
        return $this->render('index.html.twig');
    }
}