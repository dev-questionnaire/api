<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class), ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableEntity;

    #[ORM\PrePersist, ORM\PreUpdate]
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTime());

        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTime());
        }
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', length: 255)]
    private $token = '';

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $tokenTime;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserQuestion::class)]
    private $userQuestions;
    public function __construct()
    {
        $this->userQuestions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getTokenTime(): ?\DateTimeInterface
    {
        return $this->tokenTime;
    }

    public function setTokenTime(?\DateTimeInterface $tokenTime): self
    {
        $this->tokenTime = $tokenTime;

        return $this;
    }

    /**
     * @return Collection|UserQuestion[]
     */
    public function getUserQuestions(): Collection
    {
        return $this->userQuestions;
    }

    public function addNewUserQuestion(UserQuestion $newUserQuestion): self
    {
        if (!$this->userQuestions->contains($newUserQuestion)) {
            $this->userQuestions[] = $newUserQuestion;
            $newUserQuestion->setUser($this);
        }

        return $this;
    }

    public function removeNewUserQuestion(UserQuestion $newUserQuestion): self
    {
        if ($this->userQuestions->removeElement($newUserQuestion)) {
            // set the owning side to null (unless already changed)
            if ($newUserQuestion->getUser() === $this) {
                $newUserQuestion->setUser(null);
            }
        }

        return $this;
    }
}
