<?php

namespace App\Manager;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;

class EventManager extends BaseManager
{
    /**
     * @var EventRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * NewsManager constructor.
     * @param EventRepository $repository
     * @param EntityManagerInterface $em
     */
    public function __construct
    (
        EventRepository $repository,
        EntityManagerInterface $em
    )
    {
        parent::__construct($repository, $em);
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @param $lang
     * @return Event
     */
    public function homePageEvents($lang)
    {
        $event = $this->repository->getHomepageEvents($lang);
        return $event;
    }
}
