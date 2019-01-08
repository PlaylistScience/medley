<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use App\Repository\TrackRepository;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/tracks", name="api-tracks")
     */
    public function tracks(TrackRepository $trackRepository)
    {
        $tracks = $trackRepository->findAll();

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        // use first normalizer in array -- there must be a cleaner way to write this
        $normalizers[0]->setCircularReferenceHandler(function ($track) {
            return $track->getId();
        });

        $jsonContent = $serializer->serialize($tracks, 'json');

        return new Response($jsonContent);
    }
}
