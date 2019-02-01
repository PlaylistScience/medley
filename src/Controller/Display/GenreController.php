<?php

namespace App\Controller\Display;

use App\Repository\GenreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GenreController extends AbstractController
{
    /**
     * @Route("/genres", name="genres")
     */
    public function index(GenreRepository $genreRepository)
    {
        $genre = $genreRepository->findAll();

        return $this->render('genre/index.html.twig', [
            'genre' => $genre,
        ]);
    }
}
