<?php

namespace App\Entity;

use App\Repository\UserQuestionRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: UserQuestionRepository::class), ORM\HasLifecycleCallbacks]
class UserQuestion
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

    #[ORM\Column(type: 'json', nullable: true)]
    private $answers;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userQuestions')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: 'string')]
    private $examSlug;

    #[ORM\Column(type: 'string')]
    private $questionSlug;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnswers(): ?array
    {
        return $this->answers;
    }

    public function setAnswer(?string $answer): self
    {
        $this->answers[] = $answer;

        return $this;
    }

    public function setAnswers(?array $answers): self
    {
        $this->answers = $answers;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getExamSlug(): ?string
    {
        return $this->examSlug;
    }

    public function setExamSlug(?string $examSlug): self
    {
        $this->examSlug = $examSlug;

        return $this;
    }

    public function getQuestionSlug(): ?string
    {
        return $this->questionSlug;
    }

    public function setQuestionSlug(?string $questionSlug): self
    {
        $this->questionSlug = $questionSlug;

        return $this;
    }
}
