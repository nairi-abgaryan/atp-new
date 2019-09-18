<?php

namespace App\Repository;

use App\Entity\GalleryLang;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GalleryLang|null find($id, $lockMode = null, $lockVersion = null)
 * @method GalleryLang|null findOneBy(array $criteria, array $orderBy = null)
 * @method GalleryLang[]    findAll()
 * @method GalleryLang[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GalleryLangRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GalleryLang::class);
    }
}
