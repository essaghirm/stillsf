<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OldCategoryRepository")
 */
class OldCategory
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
    private $old_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $parent;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $table;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getParent(): ?int
    {
        return $this->parent;
    }

    public function setParent(?int $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getTable(): ?int
    {
        return $this->table;
    }

    public function setTable(?int $table): self
    {
        $this->table = $table;

        return $this;
    }
}
