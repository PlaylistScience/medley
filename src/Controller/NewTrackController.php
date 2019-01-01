<?php

namespace App\Controller;

// use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

use App\Form\TrackType;
use App\Entity\Track;
use App\Repository\GenreRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class NewTrackController extends AbstractController
{
    /**
     * @Route("/new/track", name="new_track")
     */
    public function index(Request $request, EntityManagerInterface $entityManager, GenreRepository $genreRepository)
    {
        // Build the form
        $track = new Track();
        $form = $this->createForm(TrackType::class, $track);

        $genre = $genreRepository->findAll();

        // handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $track = $form->getData();
            $entityManager->persist($track);
            $entityManager->flush();

            $this->addFlash(
                'notice', // flash type
                'New track has been added' // flash message
            );

            return $this->redirectToRoute('index');
        }

        return $this->render('new/track.html.twig', [
            'form' => $form->createView(),
            'genre' => $genre,
        ]);
    }
}
