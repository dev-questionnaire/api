<?php

declare(strict_types=1);

namespace App\Component\User\Communication;

use App\Component\User\Business\FacadeInterface;
use App\Controller\CustomAbstractController;
use App\DataProvider\UserDataProvider;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @psalm-suppress PropertyNotSetInConstructor */
class UserController extends CustomAbstractController
{
    public function __construct(
        private FacadeInterface $facade,
    ) {
        parent::__construct($this->facade);
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

        $userDataProvider = $this->facade->findById($id);

        if (!$userDataProvider instanceof UserDataProvider) {
            return $this->json([
                'message' => 'no user found',
            ]);
        }

        return $this->json([
            'id' => $userDataProvider->getId(),
            'email' => $userDataProvider->getEmail(),
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

        /** @var User $user */
        $user = $this->facade->findByToken($token);

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
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

        $userList = $this->facade->findAll();

        return $this->json(
            $userList
        );
    }
}
