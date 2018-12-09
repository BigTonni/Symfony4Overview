<?php

namespace App\Entity;

//use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

class User
{
    /**
     * @var int
     */
    private $id;
    
    /**
     * @var string
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    private $fullName;
    
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=50)
     */
    private $username;
    
    /**
     * @var string
     * @Assert\Email()
     */
    private $email;
    
    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $password;

    public function getId(): int
    {
        return $this->id;
    }
    
    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }
    
    public function getFullName(): ?string
    {
        return $this->fullName;
    }
    
    public function getUsername(): ?string
    {
        return $this->username;
    }
    
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }
    
    public function getEmail(): ?string
    {
        return $this->email;
    }
    
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
    
    public function getPassword(): ?string
    {
        return $this->password;
    }
    
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
