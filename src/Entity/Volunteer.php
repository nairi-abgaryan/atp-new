<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VolunteerRepository")
 */
class Volunteer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $check1;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $check2;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $check3;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $check4;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $check5;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comments;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCheck1(): ?string
    {
        return $this->check1;
    }

    public function setCheck1(?string $check1): self
    {
        $this->check1 = $check1;

        return $this;
    }

    public function getCheck2(): ?string
    {
        return $this->check2;
    }

    public function setCheck2(?string $check2): self
    {
        $this->check2 = $check2;

        return $this;
    }

    public function getCheck3(): ?string
    {
        return $this->check3;
    }

    public function setCheck3(?string $check3): self
    {
        $this->check3 = $check3;

        return $this;
    }

    public function getCheck4(): ?string
    {
        return $this->check4;
    }

    public function setCheck4(?string $check4): self
    {
        $this->check4 = $check4;

        return $this;
    }

    public function getCheck5(): ?string
    {
        return $this->check5;
    }

    public function setCheck5(?string $check5): self
    {
        $this->check5 = $check5;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
