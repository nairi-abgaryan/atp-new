<?php

namespace App\Repository;

use App\Entity\SlideLang;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SlideLang|null find($id, $lockMode = null, $lockVersion = null)
 * @method SlideLang|null findOneBy(array $criteria, array $orderBy = null)
 * @method SlideLang[]    findAll()
 * @method SlideLang[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SlideLangRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SlideLang::class);
    }
}
