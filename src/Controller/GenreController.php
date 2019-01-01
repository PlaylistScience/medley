<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\GenreRepository;

class GenreController extends AbstractController
{
    /**
     * @Route("/genres", name="genres")
     */
    public function index(GenreRepository $genreRepository)
    {
        $genre = $genreRepository->findAll();

        return $this->render('genre/index.html.twig', [
            'genre' => $genre
        ]);
    }
}
