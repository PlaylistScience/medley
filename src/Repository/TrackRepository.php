<?php

namespace App\Repository;

use App\Entity\Track;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Track|null find($id, $lockMode = null, $lockVersion = null)
 * @method Track|null findOneBy(array $criteria, array $orderBy = null)
 * @method Track[]    findAll()
 * @method Track[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Track::class);
        $this->_em = $em;
    }

    public function save(Track $track)
    {
        $this->_em->persist($track);
        $this->_em->flush();
    }

    public function fetchAvailableTracks($limit = 10)
    {
        $tracks = $this->createQueryBuilder('t')
            ->leftJoin('t.genre', 'g')
            ->leftJoin('t.owner', 'o')
            ->select('partial t.{id, name, artist, url}', 'g', 'partial o.{id}')
            ->orderBy('t.created_at', 'desc')
            ->getQuery()
            ->getArrayResult();

        return $this->setYtIdValues($tracks);
    }

    public function fetchByUserId($id)
    {
        $tracks = $this->createQueryBuilder('t')
            ->leftJoin('t.genre', 'g')
            ->leftJoin('t.owner', 'o') // join owner
            ->leftJoin('t.users', 'tu') // join user_track rel table
            ->select('partial t.{id, name, artist, url}', 'g', 'partial o.{id}', 'tu')
            ->where('tu.id = :id')
            ->orWhere('o.id = :id')
            ->setParameter('id', $id)
            ->orderBy('t.created_at', 'desc')
            ->getQuery()
            ->getArrayResult();

        return $tracks;
    }

    public function setYtIdValues($tracks = [])
    {
        // hacky
        // looping through to assign temporary yt id parameters
        // shouldn't this be part of the entities?
        // is it because i'm skipping the entity creation above?
        // then maybe I should make getYTID part of
        // a static util class or something
        $processedTracks = [];
        foreach ($tracks as $track) {
            $tempTrack = new Track();
            $tempTrack->setUrl($track['url']);
            $track['ytid'] = $tempTrack->getYTID();
            $processedTracks[] = $track;
        }

        return $processedTracks;
    }

    // /**
    //  * @return Track[] Returns an array of Track objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Track
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
