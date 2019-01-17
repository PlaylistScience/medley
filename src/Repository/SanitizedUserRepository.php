<?php

namespace App\Repository;

use App\Entity\SanitizedUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method SanitizedUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method SanitizedUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method SanitizedUser[]    findAll()
 * @method SanitizedUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SanitizedUserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, SanitizedUser::class);
        $this->_em = $em;
    }

    public function save(SanitizedUser $track)
    {
        $this->_em->persist($track);
        $this->_em->flush();
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
