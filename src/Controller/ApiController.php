<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\TrackRepository;

// use Symfony\Component\Serializer\Serializer;
// use Symfony\Component\Serializer\Encoder\XmlEncoder;
// use Symfony\Component\Serializer\Encoder\JsonEncoder;
// use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/tracks", name="api-tracks")
     */
    public function tracks(TrackRepository $trackRepository)
    {

        $tracks = $trackRepository->findAll();
        // $encoders = array(new XmlEncoder(), new JsonEncoder());
        // $normalizers = array(new ObjectNormalizer());

        // $serializer = new Serializer($normalizers, $encoders);

        return $this->json($tracks);
    }
}
