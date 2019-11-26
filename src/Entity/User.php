<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="This e-mail is already used")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=191, nullable=true)
     * @Assert\NotBlank(message = "Firstname is empty.")
     * @Assert\Length(min=3, max=30, minMessage = "Name is too short. It should have 3 characters or more.")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=191, nullable=true)
     * @Assert\NotBlank(message = "Lastname is empty.")
     * @Assert\Length(min=3, max=30, minMessage = "Name is too short. It should have 3 characters or more.")
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=191, unique=true, nullable=true)
     * @Assert\Length(min=6, max=30, minMessage = "Username is too short. It should have 6 characters or more.")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=191, nullable=true)
     */
    private $profile_image;

    /**
     * @ORM\Column(type="string", length=191, unique=true)
     * @Assert\NotBlank(message = "Email is empty.")
     * @Assert\Email(message = "Your email is not a valid email address.")
     * @Assert\Length(min=12, max=48, minMessage = "Email is too short. It should have 12 characters or more.")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=191, nullable=true)
     */
    private $password;

    /**
     * @Assert\NotBlank(allowNull = true)
     * @Assert\Length(min=8, max=4096)
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Date
     * @var string A "Y-m-d" formatted value
     */
    private $date_of_birth;

    /**
     * @ORM\Column(type="string", length=191, nullable=true)
     * @Assert\NotBlank(message = "City is empty.")
     * @Assert\Length(min=5, max=100, minMessage = "City is too short. It should have 5 characters or more.")
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=191, nullable=true)
     * @Assert\NotBlank(message = "Country is empty.")
     * @Assert\Length(min=5, max=100, minMessage = "Country is too short. It should have 5 characters or more.")
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=6, columnDefinition="ENUM('male', 'female')", nullable=true)
     * @Assert\NotBlank(message = "Gender is empty")
     * @Assert\Length(min=4, max=6)
     */
    private $gender;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @var array
     * @ORM\Column(type="simple_array")
     */
    private $roles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\News", mappedBy="user")
     */
    private $news;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Brand", mappedBy="user")
     */
    private $brands;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Model", mappedBy="user")
     */
    private $models;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="user")
     */
    private $posts;

    const ROLE_USER  = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    public function __construct()
    {
        $this->news     = new ArrayCollection();
        $this->brands   = new ArrayCollection(); 
        $this->models   = new ArrayCollection();
        $this->posts    = new ArrayCollection();
    }

    // news
    public function getNews() { return $this->news; }  

    // brand
    public function getBrands() { return $this->brands; }  

    // model
    public function getModels() { return $this->models; }

    // post
    public function getPosts() { return $this->posts; }

    // votes
    public function getVotes() { return $this->votes; }

    public function getId(): ?int { return $this->id; }

    public function getFirstname(): ?string { return $this->firstname; }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string { return $this->lastname; }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getUsername(): ?string { return $this->username; }

    public function setUsername(): self
    {
        $this->username = $this->firstname.random_int(100, 1000);
        return $this;
    }

    public function getProfileImage(): ?string { return $this->profile_image; }

    public function setProfileImage(?string $profile_image): self
    {
        $this->profile_image = $profile_image;
        return $this;
    }

    public function getEmail(): ?string { return $this->email; }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string { return $this->password; }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getPlainPassword() { return $this->plainPassword; }

    public function setPlainPassword($plainPassword) :void
    {
        $this->plainPassword = $plainPassword; 
    }

    public function getDateOfBirth(): ?\DateTimeInterface { return $this->date_of_birth; }

    public function setDateOfBirth(?\DateTimeInterface $date_of_birth): self
    {
        $this->date_of_birth = $date_of_birth;
        return $this;
    }

    public function getCity(): ?string { return $this->city; }

    public function setCity(?string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getCountry(): ?string { return $this->country; }

    public function setCountry(?string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getGender(): ?string { return $this->gender; }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface { return $this->created_at; }

    public function setCreatedAt(?\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getRoles() { return $this->roles; }

    public function setRoles(array $roles): void { $this->roles = $roles; }

    public function getSalt() { return null; }

    public function eraseCredentials() {}

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->firstname,
            $this->lastname,
            $this->username,
            $this->profile_image,
            $this->email,
            $this->password,
            $this->date_of_birth,
            $this->city,
            $this->country,
            $this->gender,
            $this->created_at
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->firstname,
            $this->lastname,
            $this->username,
            $this->profile_image,
            $this->email,
            $this->password,
            $this->date_of_birth,
            $this->city,
            $this->country,
            $this->gender,
            $this->created_at) = unserialize($serialized);
    }   

}
