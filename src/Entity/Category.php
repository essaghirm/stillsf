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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $lvl;

    /**
     * One Category has Many Categories.
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     */
    private $children;

    /**
     * Many Categories have One Category.
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Contact", mappedBy="category")
     */
    private $contacts;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $old_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $old_parent;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->children = new ArrayCollection();
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

    public function getLvl(): ?int
    {
        return $this->lvl;
    }

    public function setLvl(?int $lvl): self
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Category $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(Category $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getOldId(): ?int
    {
        return $this->old_id;
    }

    public function setOldId(?int $old_id): self
    {
        $this->old_id = $old_id;

        return $this;
    }

    public function getOldParent(): ?int
    {
        return $this->old_parent;
    }

    public function setOldParent(?int $old_parent): self
    {
        $this->old_parent = $old_parent;

        return $this;
    }
}
