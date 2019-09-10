<?php

namespace App\Repository;

use App\Entity\LessonsLang;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LessonsLang|null find($id, $lockMode = null, $lockVersion = null)
 * @method LessonsLang|null findOneBy(array $criteria, array $orderBy = null)
 * @method LessonsLang[]    findAll()
 * @method LessonsLang[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LessonsLangRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LessonsLang::class);
    }
}
