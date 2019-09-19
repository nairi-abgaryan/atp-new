<?php

namespace App\Entity;

use App\Entity\Base\BaseEntity;
use App\Entity\Base\LangEntity;
use App\Entity\Base\TextBottomEntity;
use App\Entity\Base\TextTopEntity;
use App\Entity\Base\TitleEntity;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass="App\Repository\SlideLangRepository")
 * @ORM\Table(name="slideLang")
 */
class SlideLang
{
    use LangEntity, TextBottomEntity, TextTopEntity, BaseEntity;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Slide
     * @ORM\ManyToOne(targetEntity="App\Entity\Slide", inversedBy="entityLang", cascade={"persist"})
     */
    private $slide;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Slide
     */
    public function getSlide()
    {
        return $this->slide;
    }

    /**
     * @param Slide $slide
     */
    public function setSlide(Slide $slide): void
    {
        $this->slide = $slide;
    }
}
