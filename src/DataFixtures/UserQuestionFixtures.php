<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserQuestion;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/** @psalm-suppress PropertyNotSetInConstructor */
class UserQuestionFixtures extends Fixture
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        //User
        $user = $this->userRepository->find(1);

        $userQuestion_1 = new UserQuestion();

        $userQuestion_1
            ->setAnswers(["Solid" => false, "Sexy_Programming" => false, "Single_possibility" => true, "Single_like_a_pringle" => false])
            ->setUser($user)
            ->setQuestionSlug('s_in_solid')
            ->setExamSlug('solid');

        $userQuestion_2 = new UserQuestion();

        $userQuestion_2
            ->setAnswers(['Open_relation' => true, 'Oral__ex' => false, 'Open_close' => true, 'Opfer' => false])
            ->setUser($user)
            ->setQuestionSlug('o_in_solid')
            ->setExamSlug('solid');

        $userQuestion_3 = new UserQuestion();

        $userQuestion_3
            ->setAnswers(null)
            ->setUser($user)
            ->setQuestionSlug('harun_alter')
            ->setExamSlug('harun');

        $manager->persist($userQuestion_1);
        $manager->persist($userQuestion_2);
        $manager->persist($userQuestion_3);
        $manager->flush();


        //Admin
        $user = $this->userRepository->find(2);

        $userQuestion = new UserQuestion();

        $userQuestion
            ->setAnswers(["Solid" => true, "Sexy_Programming" => false, "Single_possibility" => true, "Single_like_a_pringle" => false])
            ->setUser($user)
            ->setQuestionSlug('s_in_solid')
            ->setExamSlug('solid');

        $manager->persist($userQuestion);
        $manager->flush();
    }
}