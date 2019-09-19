<?php

namespace App\Entity;

use App\Entity\Base\BaseEntity;
use App\Entity\Base\ImageEntity;
use App\Entity\Base\LangEntity;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass="App\Repository\GalleryLangRepository")
 * @ORM\Table(name="gallaryLang")
 */
class GalleryLang
{
    use BaseEntity, LangEntity, ImageEntity;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Event
     * @ORM\ManyToOne(targetEntity="App\Entity\Gallery", inversedBy="entityLang", cascade={"persist"})
     */
    private $gallery;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->gallery;
    }

    /**
     * @param Gallery $gallery
     */
    public function setGallery(Gallery $gallery): void
    {
        $this->gallery = $gallery;
    }
}
