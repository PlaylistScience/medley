<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Track;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, User::class);
        $this->_em = $em;
    }

    public function save(User $user)
    {
        $this->_em->persist($track);
        $this->_em->flush();
    }

    public function sanitizedUser($id)
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.tracks', 'ut')
            ->select('partial u.{id, email}', 'ut')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            // get array result so that select statement above takes effect
            // otherwise with getResult() the entity is matched up and
            // the entire use entity structure gets exposed with the front end
            // ... even if the values get excludes in that case,
            // getArrayResult is better practice in this case I think
            ->getArrayResult()
        ;
    }

    public function sanitizedUsers()
    {
        // add join to statement to pull in track data also
        return $this->createQueryBuilder('u')
            ->select('partial u.{id, email}')
            ->getQuery()
            ->getArrayResult()
        ;
    }
}
