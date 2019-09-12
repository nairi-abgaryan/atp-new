<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @param $lang
     * @return Event
     */
    public function getHomepageEvents($lang)
    {
//        var_dump('aaa');die;
        $qb = $this->createQueryBuilder('e')
            ->select('e, l')
            ->join('e.entityLang', 'l')
            ->getQuery();
        $result = $qb->getResult();
        return $result;
//        $qb =
//            $this->createQueryBuilder("event")
//                ->leftJoin("event.eventLang", "lang")
//
//
//        if ($country) {
//            $qb
//                ->leftJoin("country.countryLang", "countryLang")
//                ->andWhere("countryLang.name = :country")
//                ->setParameter("country", $country);
//        }
//
//        if ($query) {
//            $qb->andWhere('cityLang.name LIKE :query');
//            $qb->setParameter('query', "%" . $query . "%");
//        }
//
//        return $qb->getQuery()->getResult();
    }

}
