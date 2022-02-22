<?php

declare(strict_types=1);

namespace App\Component\User\Communication;

use App\Component\User\Business\FacadeUserInterface;
use App\Component\User\Dependency\BridgeUserQuestionInterface;
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
        private FacadeUserInterface $facadeUser,
        private BridgeUserQuestionInterface $bridgeUserQuestion,
    ) {
        parent::__construct($this->facadeUser);
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

        $userDataProvider = $this->facadeUser->findById($id);

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
        $user = $this->facadeUser->findByToken($token);

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

        $userList = $this->facadeUser->getAllFormattedAsArray();

        return $this->json(
            $userList
        );
    }

    #[Route("/api/user/register", name: "api_user_register", methods: "POST")]
    public function register(Request $request): JsonResponse
    {
        $content = $this->getContent($request);

        /** @var string $email */
        $email = $content['email'] ?? '';
        /** @var string $password */
        $password = $content['password'] ?? '';
        /** @var string $verificationPassword */
        $verificationPassword = $content['verificationPassword'] ?? '';

        $userDataProvider = new UserDataProvider();
        $userDataProvider
            ->setEmail($email)
            ->setPassword($password)
            ->setVerificationPassword($verificationPassword);

        $errors = $this->facadeUser->create($userDataProvider);

        if (!empty($errors)) {
            return $this->json([
                'errors' => $errors,
            ]);
        }

        return $this->json([
            'created' => true,
        ]);
    }

    #[Route("/api/user/delete", name: "api_user_delete", methods: "POST")]
    public function deleteUser(Request $request): JsonResponse
    {
        $content = $this->getContent($request);

        /** @var string $token */
        $token = $content['token'] ?? '';

        $userDataProvider = $this->authenticate($token);

        if (!$userDataProvider instanceof UserDataProvider) {
            return $this->json([
                'message' => 'access not authorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        /** @var int $id */
        $id = $userDataProvider->getId();

        $this->bridgeUserQuestion->deleteByUser($id);
        $this->facadeUser->delete($id);

        return $this->json([
            'deleted' => true,
        ]);
    }
}
