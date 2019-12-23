<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ModelRepository")
 * @UniqueEntity(fields="title", message="This title is already used")
 */
class Model implements \JsonSerializable, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=191)
     * @Assert\NotBlank(message = "Title is empty.")
     * @Assert\Length(min=2, max=191, minMessage = "Title is too short. It should have 2 characters or more.")
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="models")
     * @ORM\JoinColumn()
     */     
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Brand", inversedBy="models")
     * @ORM\JoinColumn()
     */     
    private $brand;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="model", cascade={"remove"})
     */
    private $posts;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    public function __construct()
    {
        $this->posts  = new ArrayCollection();
    }

    // post
    public function getPosts() { return $this->posts; }

    public function getId(): ?int { return $this->id; }

    public function getTitle(): ?string { return $this->title; }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface { return $this->created_at; }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * @return User
    */
    public function getUser() { return $this->user; }
 
    public function setUser($user): void { $this->user = $user; }  

    /**
     * @return Brand
    */
    public function getBrand() { return $this->brand; }
 
    public function setBrand($brand): void { $this->brand = $brand; }  

    public function jsonSerialize(): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'created_at' => $this->created_at,
            'brand_id'   => $this->brand,
        ];
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->user,
            $this->title,
            $this->created_at,
            $this->brand
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->user,
            $this->title,
            $this->created_at,
            $this->brand
        ) = unserialize($serialized);
    } 

}
