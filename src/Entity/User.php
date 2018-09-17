<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(schema="symfony",name="symfony.app_users")
 * @UniqueEntity(fields="email", message="user.email.unique")
 * @UniqueEntity(fields="username", message="user.username.unique")
 * @ORM\HasLifecycleCallbacks()
 *
 * Defines the properties of the User entity to represent the application users.
 * See https://symfony.com/doc/current/book/doctrine.html#creating-an-entity-class
 *
 * Tip: if you have an existing database, you can generate these entity class automatically.
 * See https://symfony.com/doc/current/cookbook/doctrine/reverse_engineering.html
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $fullName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, unique=true)
     * @Assert\Length(
     *     min = 2,
     *     max = 50,
     *     minMessage = "The username must contain at least 2 caracters",
     *     maxMessage = "Your first name cannot be longer than {{ limit }} characters"
     * )
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     */
    private $email;

    /**
     * @Assert\Length(
     *     min = 8,
     *     max = 4096,
     *     minMessage = "The password must contain at least 8 caracters",
     *     maxMessage = "Your first name cannot be longer than {{ limit }} characters"
     * )
     * @Assert\Regex(
     *     pattern       = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[_$&+,:;=?@#|'<>.^*()%!-])([-$&+,:;=?@#|'<>.^*()%!_\w]{8,4096})$/i",
     *     htmlPattern   =  "^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[_$&+,:;=?@#|'<>.^*()%!-])([-$&+,:;=?@#|'<>.^*()%!_\w]{8,4096})$",
     *     message       = "The password must contain at least a number, an uppercase, a lowercase and a special caracter (_$&+,:;=?@#|'<>.^*()%!-)"
     * )
     */
    private $plainPassword;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $token;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @var \datetime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @var \datetime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \datetime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var \datetime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $credentialExpireAt;

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true, options={"default" : "en"})
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true, options={"default" : "default"})
     */
    private $entityManager;

    public function __construct() {
        $this->username = "";
        $this->email = "";
        $this->plainPassword = "";
        $this->createdAt = new \DateTime('now');
        $this->lastLogin = null;
        $this->updatedAt = null;
        $this->credentialExpireAt = null;
        $this->token = null;
        $this->language = 'en';
        $this->entityManager = 'default';
    }

    public function getId(): ?int
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

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * Returns the roles or permissions granted to the user for security.
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        // guarantees that a user always has at least one role for security
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        // See "Do you need to use a Salt?" at https://symfony.com/doc/current/cookbook/security/entity_provider.html
        // we're using bcrypt in security.yml to encode the password, so
        // the salt value is built-in and you don't have to generate one

        return null;
    }

    /**
     * AdvancedUserInterface
     */
     public function isAccountNonExpired()
     {
         return true;
     }

     public function isAccountNonLocked()
     {
         return true;
     }

     public function isCredentialsNonExpired()
     {
         return true;
     }

     public function isEnabled()
     {
         return $this->isActive;
     }

    /**
     * Removes sensitive data from the user.
     *
     * {@inheritdoc}
     * @ORM\PostUpdate
     */
    public function eraseCredentials(): void
    {
        // if you had a plainPassword property, you'd nullify it here
        $this->plainPassword = null;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        return serialize([$this->id, $this->username, $this->password,$this->isActive]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        [$this->id, $this->username, $this->password,$this->isActive,] = unserialize($serialized, ['allowed_classes' => false]);
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function initCreatedAt(): self
    {
        $this->createdAt = new \DateTime('now');

         return $this;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PreFlush
     */
    public function updateUpdatedAt(): self
    {
        $this->updatedAt = new \DateTime('now');

         return $this;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCredentialExpireAt(): ?\DateTimeInterface
    {
        return $this->credentialExpireAt;
    }

    public function setCredentialExpireAt(?\DateTimeInterface $credentialExpireAt): self
    {
        $this->credentialExpireAt = $credentialExpireAt;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function hasRole(?string $role) : bool
    {
        if( in_array($role, $this->roles) ) return true;
        else return false;
    }
}
