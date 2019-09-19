<?php

namespace App\Entity;

use App\Entity\Base\BaseEntityVirtual;
use App\Entity\Base\ImageEntity;
use App\Entity\Base\TextBottomEntityVirtual;
use App\Entity\Base\TextTopVirtualEntity;
use App\Entity\Base\TimestampableEntity;
use App\Entity\Base\TitleEntityVirtual;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SlideRepository")
 * @Vich\Uploadable
 */
class Slide
{
    use BaseEntityVirtual, ImageEntity, TimestampableEntity, TextBottomEntityVirtual, TextTopVirtualEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\SlideLang",
     *     mappedBy="slide",
     *     cascade={"persist", "remove", "refresh", "merge"},
     *     fetch="EXTRA_LAZY",
     *     orphanRemoval=true
     * )
     */
    public $entityLang;

    /**
     * EducationContentBottom constructor.
     */
    public function __construct()
    {
        $this->entityLang = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return ArrayCollection
     */
    public function getEntityLang()
    {
        return $this->entityLang;
    }

    /**
     * @param SlideLang $entityLang
     */
    public function addEntityLang(SlideLang $entityLang): void
    {
        $entityLang->setSlide($this);
        $this->entityLang->set(0, $entityLang);
    }
}
