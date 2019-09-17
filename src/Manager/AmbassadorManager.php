<?php

namespace App\Manager;

use App\Entity\Ambassador;
use App\Repository\AmbassadorRepository;
use Doctrine\ORM\EntityManagerInterface;

class AmbassadorManager extends BaseManager
{
    /**
     * @var AmbassadorRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * AmbassadorManager constructor.
     * @param AmbassadorRepository $repository
     * @param EntityManagerInterface $em
     */
    public function __construct
    (
        AmbassadorRepository $repository,
        EntityManagerInterface $em
    )
    {
        parent::__construct($repository, $em);
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @param $lang
     * @return Ambassador
     */
    public function getAmbassadors($lang)
    {
        $Ambassador = $this->repository->getAmbassadors($lang);
        return $Ambassador;
    }
}
