<?php

// src/Repository/UserRepository.php
namespace App\Repository;

use App\Entity\User;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
#use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

#class UserRepository extends EntityRepository implements UserLoaderInterface
class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
	public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
