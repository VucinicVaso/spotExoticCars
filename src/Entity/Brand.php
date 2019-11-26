<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BrandRepository")
 */
class Brand implements \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=191, nullable=true)
     * @Assert\NotBlank(message = "Title is empty.")
     * @Assert\Length(min=3, max=191, minMessage = "Title is too short. It should have 3 characters or more.")
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Model", mappedBy="brand")
     */
    private $models;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="brand")
     */
    private $posts;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="brands")
     * @ORM\JoinColumn()
     */     
    private $user;

    public function __construct()
    {
        $this->models = new ArrayCollection(); 
        $this->posts  = new ArrayCollection();
    }

    // model
    public function getModels() { return $this->models; }

    // post
    public function getPosts() { return $this->posts; }

    public function getId(): ?int { return $this->id; }

    public function getTitle(): ?string { return $this->title; }

    public function setTitle(?string $title): self
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

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->title,
            $this->created_at,
            $this->user
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->title,
            $this->created_at,
            $this->user
        ) = unserialize($serialized);
    } 

}
