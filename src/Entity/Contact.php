<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\Column(type="string", length=191, nullable=true)
     * @Assert\NotBlank(message = "Name is empty.")
     * @Assert\Length(min=3, max=191, minMessage = "Name is too short. It should have 3 characters or more.")
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
     * @ORM\Column(type="string", length=191, nullable=true)
     * @Assert\NotBlank(message = "Subject is empty.")
     * @Assert\Length(min=4, max=191, minMessage = "Subject is too short. It should have 4 characters or more.")
     */
    private $subject;

    /**
     * @ORM\Column(type="string", length=191, nullable=true)
     * @Assert\NotBlank(message = "Message is empty.")
     * @Assert\Length(min=4, max=191, minMessage = "Message is too short. It should have 4 characters or more.")
     */
    private $message;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): ?string { return $this->email; }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getSubject(): ?string { return $this->subject; }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function getMessage(): ?string { return $this->message; }

    public function setMessage(?string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface { return $this->created_at; }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

}
