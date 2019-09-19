<?php

namespace App\Entity;

use App\Entity\Base\BaseEntity;
use App\Entity\Base\LangEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LessonsLangRepository")
 * @ORM\Table(name="lessonsLang")
 */
class LessonsLang
{
    use BaseEntity, LangEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Lessons
     * @ORM\ManyToOne(targetEntity="App\Entity\Lessons", inversedBy="entityLang", cascade={"persist"})
     */
    private $lessons;

    /**
     * @ORM\Column(type="string", nullable=true, length=2)
     */
    private $lang;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return Lessons
     */
    public function getLessons()
    {
        return $this->lessons;
    }

    /**
     * @param Lessons $lessons
     */
    public function setLessons(Lessons $lessons): void
    {
        $this->lessons = $lessons;
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
