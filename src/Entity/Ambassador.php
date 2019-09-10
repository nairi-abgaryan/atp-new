<?php

namespace App\Entity;

use App\Entity\Base\BaseVirtual;
use App\Entity\Base\ImageEntity;
use App\Entity\Base\TextBottomEntityVirtual;
use App\Entity\Base\TextTopVirtualEntity;
use App\Entity\Base\TimestampableEntity;
use App\Entity\Base\TitleEntityVirtual;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AmbassadorRepository")
 * @ORM\Table(name="ambassadors")
 * @Vich\Uploadable
 */
class Ambassador
{
    use TitleEntityVirtual,
        TextBottomEntityVirtual,
        TextTopVirtualEntity,
        TimestampableEntity,
        ImageEntity,
        BaseVirtual;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\AmbassadorLang",
     *     mappedBy="ambassador",
     *     cascade={"persist", "remove", "refresh", "merge"},
     *     fetch="EXTRA_LAZY",
     *     orphanRemoval=true
     * )
     */
    public $entityLang;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="App\Entity\Image", cascade={"persist"})
     * @ORM\JoinTable(name="ambassador_images",
     *      joinColumns={@ORM\JoinColumn(name="ambassador_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id", unique=true)}
     * )
     */
    private $images;

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
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param ArrayCollection $images
     */
    public function setImages($images): void
    {
        $this->images = $images;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getEntityLang()
    {
        return $this->entityLang;
    }

    /**
     * @param AmbassadorLang $entityLang
     */
    public function addEntityLang(AmbassadorLang $entityLang): void
    {
        $entityLang->setAmbassador($this);
        $this->entityLang->set(0, $entityLang);
    }
}
