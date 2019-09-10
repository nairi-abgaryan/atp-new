<?php

namespace App\Entity;

use App\Entity\Base\BaseEntityVirtual;
use App\Entity\Base\ImageEntity;
use App\Entity\Base\TimestampableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LessonsRepository")
 * @Vich\Uploadable
 */
class Lessons
{
    use BaseEntityVirtual, TimestampableEntity, ImageEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\NewsLang",
     *     mappedBy="news",
     *     cascade={"persist", "remove", "refresh", "merge"},
     *     fetch="EXTRA_LAZY",
     *     orphanRemoval=true
     * )
     * @Serializer\Accessor(setter="setEntityLang")
     */
    public $entityLang;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="App\Entity\Image", cascade={"persist"})
     */
    private $images;

    /**
     * @ORM\Column(type="string", length=256, nullable=true)
     * @var string
     */
    private $pdf;

    /**
     * @Vich\UploadableField(mapping="images", fileNameProperty="pdf")
     * @var File
     */
    private $pdfFile;

    /**
     * @ORM\Column(type="string", length=256, nullable=true)
     * @var string
     */
    private $pdfName;

    /**
     * Country constructor.
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

    public function __toString()
    {
        return $this->title;
    }

    /**
     * @return File
     */
    public function getPdfFile()
    {
        return $this->pdfFile;
    }

    /**
     * @param File $pdfFile
     */
    public function setPdfFile($pdfFile)
    {
        $this->pdfFile = $pdfFile;
    }

    /**
     * @return string
     */
    public function getPdf()
    {
        return $this->pdf;
    }

    /**
     * @param string $pdf
     */
    public function setPdf($pdf)
    {
        $this->pdf = $pdf;
    }

    /**
     * @return string
     */
    public function getPdfName()
    {
        return $this->pdfName;
    }

    /**
     * @param string $pdfName
     */
    public function setPdfName($pdfName)
    {
        $this->pdfName = $pdfName;
    }

    /**
     * @return ArrayCollection
     */
    public function getEntityLang()
    {
        return $this->entityLang;
    }

    /**
     * @param NewsLang $entityLang
     */
    public function addEntityLang(NewsLang $entityLang): void
    {
        $entityLang->setNews($this);
        $this->entityLang->set(0, $entityLang);
    }
}
