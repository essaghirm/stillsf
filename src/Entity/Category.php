<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $lft;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rgt;

    /**
     * @ORM\OneToMany(targetEntity="Contact", mappedBy="category")
     */
    private $contacts;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getLft()
    {
        return $this->lft;
    }

    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    public function getRgt()
    {
        return $this->rgt;
    }

    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * @return Collection|Contact[]
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setCategory($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->contains($contact)) {
            $this->contacts->removeElement($contact);
            // set the owning side to null (unless already changed)
            if ($contact->getCategory() === $this) {
                $contact->setCategory(null);
            }
        }

        return $this;
    }
}
