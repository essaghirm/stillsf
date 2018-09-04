<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CatContRepository")
 */
class CatCont
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $contact;

    /**
     * @ORM\Column(type="integer")
     */
    private $category;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContact(): ?int
    {
        return $this->contact;
    }

    public function setContact(int $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public function getCategory(): ?int
    {
        return $this->category;
    }

    public function setCategory(int $category): self
    {
        $this->category = $category;

        return $this;
    }

}
