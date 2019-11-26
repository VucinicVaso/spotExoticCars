<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment implements \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=191, nullable=true)
     * @Assert\NotBlank(message = "Name is empty.")
     * @Assert\Length(min=3, max=48, minMessage = "Name is too short. It should have 3 characters or more.")
     */
    private $name;

    /**     
     * @ORM\Column(type="string", length=191, nullable=true)
     * @Assert\NotBlank(message = "Email is empty.")
     * @Assert\Email(message = "Your email is not a valid email address.")
     * @Assert\Length(min=12, max=48, minMessage = "Email is too short. It should have 12 characters or more.")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=191)
     * @Assert\NotBlank(message = "Comment is empty.")
     * @Assert\Length(min=3, max=48, minMessage = "Comment is too short. It should have 3 characters or more.")
     */
    private $comment;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Post", inversedBy="comments")
     * @ORM\JoinColumn()
     */     
    private $post;

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): ?string { return $this->email; }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getComment(): ?string { return $this->comment; }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface { return $this->created_at; }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * @return Post
    */
    public function getPost() { return $this->post; }
 
    public function setPost($post): void { $this->post = $post; }  

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->post,
            $this->name,
            $this->email,
            $this->comment,
            $this->created_at
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->post,
            $this->name,
            $this->email,
            $this->comment,
            $this->created_at
        ) = unserialize($serialized);
    } 

}
