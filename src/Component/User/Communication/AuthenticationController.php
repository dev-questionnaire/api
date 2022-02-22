<?php

declare(strict_types=1);

namespace App\Component\User\Communication;

use App\Component\User\Business\FacadeUserInterface;
use App\Controller\CustomAbstractController;
use App\DataProvider\UserDataProvider;
use App\Entity\User;
use App\Service\App;
use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

/** @psalm-suppress PropertyNotSetInConstructor */
class AuthenticationController extends CustomAbstractController
{
    public function __construct(
        private FacadeUserInterface $facadeUser,
        private App                 $app,
    ) {
        parent::__construct($this->facadeUser);
    }

    #[Route('/api/login', name: 'api_login', methods: 'POST')]
    public function login(#[CurrentUser] User $currentUser): JsonResponse
    {
        $user = $currentUser;
        $email = $user->getEmail() ?? '';

        $payload = [
            'userId' => $user->getId(),
            'time' => (new \DateTime())->format('Y-m-d_H:i:s'),
        ];
        /** @var string $kernelSecret */
        $kernelSecret = $this->getParameter('kernel.secret');


        $jwt = JWT::encode($payload, $kernelSecret, 'HS256');

        $this->facadeUser->setToken($email, $jwt);

        $tokenId = $email . '-' . (new \DateTime())->format('Y-m-d_H-i-s');

        $content = $this->app->sendTokenId($tokenId, $jwt);

        if (!isset($content['generated']) || !$content['generated']) {
            return $this->json([
                'success' => false,
            ]);
        }

        return $this->json([
            'success' => true,
            'tokenId' => $tokenId,
        ]);
    }

    #[Route('/api/authorized', name: 'api_authenticate', methods: 'POST')]
    public function authorized(Request $request): JsonResponse
    {
        $content = $this->getContent($request);

        /** @var string $token */
        $token = $content['token'] ?? '';
        /** @var string $role */
        $role = $content['role'] ?? 'ROLE_USER';

        if ($token === '') {
            return $this->json([
                'authorized' => false,
            ], Response::HTTP_UNAUTHORIZED);
        }

        $authenticate = $this->authenticate($token, $role);

        if ($authenticate === false) {
            return $this->json([
                'authorized' => false,
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'authorized' => true,
        ], Response::HTTP_ACCEPTED);
    }

    #[Route('/api/logout', name: 'api_logout', methods: 'POST')]
    public function logout(Request $request): JsonResponse
    {
        $content = $this->getContent($request);

        /** @var string $token */
        $token = $content['token'] ?? '';

        $userDataProvider = $this->facadeUser->findByToken($token);

        if (!$userDataProvider instanceof UserDataProvider) {
            return $this->json([
                'logout' => false,
                'error' => 'user not logged in',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $this->facadeUser->removeToken($token);

        return $this->json([
            'logout' => true,
        ]);
    }
}
