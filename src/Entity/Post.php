<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 */
class Post implements \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=191)
     * @Assert\NotBlank(message = "City is empty.")
     * @Assert\Length(min=5, max=100, minMessage = "City is too short. It should have 5 characters or more.")
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=191)
     * @Assert\NotBlank(message = "Country is empty.")
     * @Assert\Length(min=5, max=100, minMessage = "Country is too short. It should have 5 characters or more.")
     */
    private $country;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $images = [];

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $views;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="post", cascade={"remove"})
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn()
     */     
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Brand", inversedBy="posts")
     * @ORM\JoinColumn()
     */     
    private $brand;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Model", inversedBy="posts")
     * @ORM\JoinColumn()
     */     
    private $model;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getComments() { return $this->comments; }

    public function getId(): ?int { return $this->id; }

    public function getCity(): ?string { return $this->city; }

    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getCountry(): ?string { return $this->country; }

    public function setCountry(string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface { return $this->created_at; }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getImages(): ?array { return $this->images; }

    public function setImages(?array $images): self
    {
        $this->images = $images;
        return $this;
    }

    public function getViews(): ?int { return $this->views; }

    public function setViews(?int $views): self
    {
        $this->views = $views;
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

    /**
     * @return Model
    */
    public function getModel() { return $this->model; }
 
    public function setModel($model): void { $this->model = $model; }  

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->user,
            $this->city,
            $this->country,
            $this->brand,
            $this->model,
            $this->created_at,
            $this->images,
            $this->views
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->user,
            $this->city,
            $this->country,
            $this->brand,
            $this->model,
            $this->created_at,
            $this->images,
            $this->views
        ) = unserialize($serialized);
    }

}
