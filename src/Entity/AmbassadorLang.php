<?php

namespace App\Entity;

use App\Entity\Base\LangEntity;
use App\Entity\Base\TextBottomEntity;
use App\Entity\Base\TextTopEntity;
use App\Entity\Base\TitleEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AmbassadorLangRepository")
 * @ORM\Table(name="ambassadorsLang")
 */
class AmbassadorLang
{
    use TitleEntity, TextBottomEntity, TextTopEntity, LangEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Ambassador
     * @ORM\ManyToOne(targetEntity="App\Entity\Ambassador", inversedBy="entityLang", cascade={"persist"})
     */
    private $ambassador;

    /**
     * @ORM\Column(type="string", nullable=true, length=2)
     */
    private $lang;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Ambassador
     */
    public function getAmbassador()
    {
        return $this->ambassador;
    }

    /**
     * @param Ambassador $ambassador
     */
    public function setAmbassador(Ambassador $ambassador): void
    {
        $this->ambassador = $ambassador;
    }

    /**
     * @return mixed
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param mixed $lang
     */
    public function setLang($lang): void
    {
        $this->lang = $lang;
    }
}
