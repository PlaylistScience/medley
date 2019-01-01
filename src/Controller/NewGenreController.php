<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

use App\Form\GenreType;
use App\Entity\Genre;

class NewGenreController extends AbstractController
{
    /**
     * @Route("/new/genre", name="new_genre")
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        // Build the form
        $genre = new Genre();
        $form = $this->createForm(GenreType::class, $genre);

        // handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $genre = $form->getData();
            $entityManager->persist($genre);
            $entityManager->flush();

            $this->addFlash(
                'notice', // flash type
                'New genre has been added' // flash message
            );

            return $this->redirectToRoute('genres');
        }

        return $this->render('new/genre.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
