<?php

declare(strict_types=1);

namespace App\Component\UserQuestion\Persistence\Mapper;

use App\DataProvider\UserQuestionDataProvider;
use App\Entity\User;
use App\Entity\UserQuestion;

class UserQuestionMapperToDataProvider
{
    public function map(UserQuestion $userQuestion): UserQuestionDataProvider
    {
        $userQuestionDataProvider = new UserQuestionDataProvider();

        $userQuestionDataProvider
            ->setId($userQuestion->getId())
            ->setQuestionSlug($userQuestion->getQuestionSlug())
            ->setExamSlug($userQuestion->getExamSlug())
            ->setAnswers($userQuestion->getAnswers())
            ->setCreatedAt($userQuestion->getCreatedAt()->format('d.m.Y'))
            ->setUpdatedAt($userQuestion->getUpdatedAt()->format('d.m.Y'));

        $user = $userQuestion->getUser();

        if (!$user instanceof  User) {
            throw new \RuntimeException("User not provided");
        }

        $userQuestionDataProvider->setUserId($user->getId());

        return $userQuestionDataProvider;
    }
}
