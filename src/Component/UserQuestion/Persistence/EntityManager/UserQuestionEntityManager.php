<?php

declare(strict_types=1);

namespace App\Component\UserQuestion\Persistence\EntityManager;

use App\DataProvider\UserQuestionDataProvider;
use App\Entity\User;
use App\Entity\UserQuestion;
use App\Repository\UserQuestionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserQuestionEntityManager implements UserQuestionEntityManagerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserQuestionRepository $userQuestionRepository,
        private UserRepository $userRepository,
    ) {
    }

    public function create(UserQuestionDataProvider $userQuestionDataProvider): void
    {
        $userQuestion = new UserQuestion();

        $user = $this->userRepository->find($userQuestionDataProvider->getUserId());

        if(!$user instanceof User) {
            throw new \RuntimeException("User not found");
        }

        if(empty($userQuestionDataProvider->getExamSlug())) {
            throw new \RuntimeException("No exam slug provided");
        }

        if(empty($userQuestionDataProvider->getQuestionSlug())) {
            throw new \RuntimeException("No question slug provided");
        }

        $userQuestion
            ->setUser($user)
            ->setQuestionSlug($userQuestionDataProvider->getQuestionSlug())
            ->setExamSlug($userQuestionDataProvider->getExamSlug())
            ->setAnswers($userQuestionDataProvider->getAnswers());

        $this->entityManager->persist($userQuestion);
        $this->entityManager->flush();
    }

    public function updateAnswer(UserQuestionDataProvider $userQuestionDataProvider): void
    {
        $userQuestion = $this->userQuestionRepository->find($userQuestionDataProvider->getId());

        if (!$userQuestion instanceof UserQuestion) {
            throw new \RuntimeException("UserQuestion not found");
        }

        $userQuestion->setAnswers($userQuestionDataProvider->getAnswers());

        $this->entityManager->flush();
    }

    public function deleteByUser(int $userId): void
    {
        $user = $this->userRepository->find($userId);

        if(!$user instanceof User) {
            throw new \RuntimeException("User not found");
        }

        $userQuestionList = $this->userQuestionRepository->findBy(['user' => $user]);

        foreach ($userQuestionList as $userQuestion) {
            $this->entityManager->remove($userQuestion);
        }

        $this->entityManager->flush();
    }
}
