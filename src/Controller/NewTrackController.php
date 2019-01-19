<?php

namespace App\Controller;

// use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

use App\Form\TrackType;
use App\Entity\Track;
use App\Repository\GenreRepository;
use App\Repository\TrackRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use \DateTime;

class NewTrackController extends AbstractController
{
    /**
     * @Route("/new/track", name="new_track")
     */
    public function index(Request $request, TrackRepository $trackRepository, GenreRepository $genreRepository)
    {
        // Build the form
        $track = new Track();
        $form = $this->createForm(TrackType::class, $track);

        // handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $track = $form->getData();
            $track->setOwner($this->getUser());
            $track->setCreatedAt(new DateTime('now'));
            $trackRepository->save($track);

            $this->addFlash(
                'notice', // flash type
                'New track has been added' // flash message
            );

            return $this->redirectToRoute('index');
        }

        return $this->render('new/track.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
