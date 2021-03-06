<?php

namespace App\Entity;

use App\Entity\Base\BaseEntity;
use App\Entity\Base\LangEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TeamLangRepository")
 * @ORM\Table(name="teamsLang")
 */
class TeamLang
{
    use BaseEntity, LangEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Team
     * @ORM\ManyToOne(targetEntity="App\Entity\Team", inversedBy="entityLang", cascade={"persist"})
     */
    private $team;

    /**
     * @ORM\Column(type="string", nullable=true, length=2)
     */
    private $lang;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Team
     */
    public function getTeam(): Team
    {
        return $this->team;
    }

    /**
     * @param Team $team
     */
    public function setTeam(Team $team): void
    {
        $this->team = $team;
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
