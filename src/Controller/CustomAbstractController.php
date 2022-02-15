<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/** @psalm-suppress PropertyNotSetInConstructor */
class CustomAbstractController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    protected function authenticate(string $token, string $role = 'ROLE_USER'): bool
    {
        $user = $this->userRepository->findOneBy(['token' => $token]);

        if (!$user instanceof User) {
            return false;
        }

        if (new \DateTime() > $user->getTokenTime()) {
            return false;
        }

        if (in_array($role, $user->getRoles(), true) === false) {
            return false;
        }

        $user->setTokenTime(new \DateTime('+ 60 Minutes'));
        $this->entityManager->flush();

        return true;
    }

    /**
     * @return array<array-key, mixed>
     */
    protected function getContent(Request $request): array
    {
        $content = $request->getContent();

        if (!is_string($content) || $content === "") {
            return [];
        }

        return (array)json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }
}
