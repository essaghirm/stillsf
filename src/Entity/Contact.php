<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContactRepository")
 */
class Contact
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $web_site;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mobile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $landline;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="contacts")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="Info", mappedBy="contact")
     */
    private $infos;

    /**
     * @ORM\OneToMany(targetEntity="Relation", mappedBy="contact")
     */
    private $myFriends;

    /**
     * @ORM\OneToMany(targetEntity="Relation", mappedBy="contact")
     */
    private $friendsWithMe;

    public function __construct()
    {
        $this->infos = new ArrayCollection();
        $this->myFriends = new ArrayCollection();
        $this->friendsWithMe = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFname()
    {
        return $this->fname;
    }

    public function setFname(string $fname)
    {
        $this->fname = $fname;

        return $this;
    }

    public function getLname()
    {
        return $this->lname;
    }

    public function setLname($lname)
    {
        $this->lname = $lname;

        return $this;
    }

    public function getWebSite()
    {
        return $this->web_site;
    }

    public function setWebSite($web_site)
    {
        $this->web_site = $web_site;

        return $this;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCity(string $city)
    {
        $this->city = $city;

        return $this;
    }

    public function getNotes()
    {
        return $this->notes;
    }

    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Info[]
     */
    public function getInfos(): Collection
    {
        return $this->infos;
    }

    public function addInfo(Info $info): self
    {
        if (!$this->infos->contains($info)) {
            $this->infos[] = $info;
            $info->setContact($this);
        }

        return $this;
    }

    public function removeInfo(Info $info): self
    {
        if ($this->infos->contains($info)) {
            $this->infos->removeElement($info);
            // set the owning side to null (unless already changed)
            if ($info->getContact() === $this) {
                $info->setContact(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Relation[]
     */
    public function getMyFriends(): Collection
    {
        return $this->myFriends;
    }

    public function addMyFriend(Relation $myFriend): self
    {
        if (!$this->myFriends->contains($myFriend)) {
            $this->myFriends[] = $myFriend;
            $myFriend->setContact($this);
        }

        return $this;
    }

    public function removeMyFriend(Relation $myFriend): self
    {
        if ($this->myFriends->contains($myFriend)) {
            $this->myFriends->removeElement($myFriend);
            // set the owning side to null (unless already changed)
            if ($myFriend->getContact() === $this) {
                $myFriend->setContact(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Relation[]
     */
    public function getFriendsWithMe(): Collection
    {
        return $this->friendsWithMe;
    }

    public function addFriendsWithMe(Relation $friendsWithMe): self
    {
        if (!$this->friendsWithMe->contains($friendsWithMe)) {
            $this->friendsWithMe[] = $friendsWithMe;
            $friendsWithMe->setContact($this);
        }

        return $this;
    }

    public function removeFriendsWithMe(Relation $friendsWithMe): self
    {
        if ($this->friendsWithMe->contains($friendsWithMe)) {
            $this->friendsWithMe->removeElement($friendsWithMe);
            // set the owning side to null (unless already changed)
            if ($friendsWithMe->getContact() === $this) {
                $friendsWithMe->setContact(null);
            }
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getLandline(): ?string
    {
        return $this->landline;
    }

    public function setLandline(?string $landline): self
    {
        $this->landline = $landline;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

}
