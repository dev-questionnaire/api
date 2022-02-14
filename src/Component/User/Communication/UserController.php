<?php

declare(strict_types=1);

namespace App\Component\User\Communication;

use App\Controller\CustomAbstractController;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @psalm-suppress PropertyNotSetInConstructor */
class UserController extends CustomAbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct($this->userRepository, $this->entityManager);
    }

    #[Route('/api/user/findById', name: 'api_user_findById', methods: 'GET')]
    public function findById(Request $request): JsonResponse
    {
        $content = $this->getContent($request);

        /** @var int $id */
        $id = $content['id'] ?? 0;
        /** @var string $token */
        $token = $content['token'] ?? '';

        $authenticate = $this->authenticate($token, 'ROLE_ADMIN');

        if (!$authenticate) {
            return $this->json([
                'message' => 'access not authorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($id === 0) {
            return $this->json([
                'message' => 'no id provided',
            ]);
        }

        $user = $this->userRepository->find($id);

        if (!$user instanceof User) {
            return $this->json([
                'message' => 'no user found',
            ]);
        }

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
        ]);
    }

    #[Route('/api/user/findByToken', name: 'api_user_findByToken', methods: 'GET')]
    public function findByToken(Request $request): JsonResponse
    {
        $content = $this->getContent($request);

        /** @var string $token */
        $token = $content['token'] ?? '';

        $authenticate = $this->authenticate($token);

        if (!$authenticate) {
            return $this->json([
                'message' => 'access not authorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->userRepository->findOneBy(['token' => $token]);

        if (!$user instanceof User) {
            return $this->json([
                'message' => 'no user found',
            ]);
        }

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
        ]);
    }

    #[Route('/api/user/checkEmailTaken', name: 'api_user_checkEmailTaken', methods: 'GET')]
    public function checkEmailTaken(Request $request): JsonResponse
    {
        $content = $this->getContent($request);

        /** @var int $id */
        $id = $content['id'] ?? 0;
        /** @var string $email */
        $email = $content['email'] ?? '';
        /** @var string $token */
        $token = $content['token'] ?? '';

        $authenticate = $this->authenticate($token);

        if (!$authenticate) {
            return $this->json([
                'message' => 'access not authorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($email === '' || $id === 0) {
            return $this->json([
                'message' => 'no id or email provided',
            ]);
        }

        $user = $this->userRepository->findByEmailExcludeId($email, $id);

        if (empty($user)) {
            return $this->json([
                'found' => false,
            ]);
        }

        return $this->json([
            'found' => true,
        ]);
    }

    #[Route('/api/user/findAll', name: 'api_user_findAll', methods: 'GET')]
    public function findAll(Request $request): JsonResponse
    {
        $content = $this->getContent($request);

        /** @var string $token */
        $token = $content['token'] ?? '';

        $authenticate = $this->authenticate($token, 'ROLE_ADMIN');

        if (!$authenticate) {
            return $this->json([
                'message' => 'access not authorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $userListJson = [];

        $userList = $this->userRepository->findAll();

        foreach ($userList as $key => $user) {
            $id = $user->getId() ?? $key;

            $userListJson[$id]['id'] = $id;
            $userListJson[$id]['email'] = $user->getEmail();
        }

        return $this->json(
            $userListJson
        );
    }
}
