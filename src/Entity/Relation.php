<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="IDX_UNQ_CONTACT_FRIEND", columns={"contact_id", "friend_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\RelationRepository")
 */
class Relation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Contact", inversedBy="myFriends")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id", nullable=false)
     */
    private $contact;

    /**
     * @ORM\ManyToOne(targetEntity="Contact", inversedBy="friendsWithMe")
     * @ORM\JoinColumn(name="friend_id", referencedColumnName="id", nullable=false)
     */
    private $friend;



    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $occupation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOccupation(): ?string
    {
        return $this->occupation;
    }

    public function setOccupation(string $occupation): self
    {
        $this->occupation = $occupation;

        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public function getFriend(): ?Contact
    {
        return $this->friend;
    }

    public function setFriend(?Contact $friend): self
    {
        $this->friend = $friend;

        return $this;
    }
}
