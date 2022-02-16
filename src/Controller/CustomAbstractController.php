<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\User\Business\FacadeInterface;
use App\Component\User\Persistence\Repository\UserRepositoryInterface;
use App\DataProvider\UserDataProvider;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/** @psalm-suppress PropertyNotSetInConstructor */
class CustomAbstractController extends AbstractController
{
    public function __construct(
        private FacadeInterface $facade,
    ) {
    }

    protected function authenticate(string $token, string $role = 'ROLE_USER'): bool
    {
        $userDataProvider = $this->facade->findByToken($token);

        if (!$userDataProvider instanceof UserDataProvider) {
            return false;
        }

        if (new \DateTime() > $userDataProvider->getTokenTime()) {
            return false;
        }

        if (in_array($role, $userDataProvider->getRoles(), true) === false) {
            return false;
        }

        $this->facade->extendLoggedInTime($token);

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
