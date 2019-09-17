<?php

namespace App\Repository;

use App\Entity\Ambassador;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Ambassador|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ambassador|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ambassador[]    findAll()
 * @method Ambassador[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AmbassadorRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Ambassador::class);
    }
    /**
     * @param $lang
     * @return Ambassador
     */
    public function getAmbassadors($lang)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.id, a.image, l.textBottom, l.textTop, l.title')
            ->leftJoin('a.entityLang', 'l')
            ->where("l.lang = :lang")
            ->setParameter('lang', $lang)
            ->getQuery();
        $result = $qb->getResult();

        return $result;
    }
}
