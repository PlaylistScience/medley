<?php

namespace App\Repository;

use App\Entity\Track;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

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
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function sanitizedUser($id)
    {
        // does not include track relationships
        return $this->createQueryBuilder('u')
            ->select('partial u.{id, username}')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult();
    }

    public function sanitizedUsers()
    {
        // add join to statement to pull in track data also
        return $this->createQueryBuilder('u')
            ->select('partial u.{id, username}')
            ->getQuery()
            ->getArrayResult();
    }
}
