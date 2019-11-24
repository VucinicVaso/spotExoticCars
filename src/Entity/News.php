<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NewsRepository")
 */
class News implements \Serializable
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $body;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $images = [];

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="news")
     * @ORM\JoinColumn()
     */     
    private $user;

    public function getId(): ?int { return $this->id; }

    public function getTitle(): ?string { return $this->title; }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getBody(): ?string { return $this->body; }

    public function setBody(?string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function getImages(): ?array { return $this->images; }

    public function setImages(?array $images): self
    {
        $this->images = $images;
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
            $this->body,
            $this->images,
            $this->created_at
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->title,
            $this->body,
            $this->images,
            $this->created_at
        ) = unserialize($serialized);
    } 

}
