<?php

declare(strict_types=1);

namespace App\Component\User\Communication;

use App\Controller\CustomAbstractController;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\App;
use Doctrine\ORM\EntityManagerInterface;
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
        private EntityManagerInterface $entityManager,
        private UserRepository         $userRepository,
        private App                    $app,
    ) {
        parent::__construct($this->userRepository, $this->entityManager);
    }

    #[Route('/api/login', name: 'api_login', methods: 'POST')]
    public function login(#[CurrentUser] ?User $currentUser): JsonResponse
    {
        /** @var User $user */
        $user = $currentUser;

        //create token
        $payload = [
            'userId' => $user->getId(),
            'time' => (new \DateTime())->format('Y-m-d_H:i:s'),
        ];

        /** @var string $kernelSecret */
        $kernelSecret = $this->getParameter('kernel.secret');

        $jwt = JWT::encode($payload, $kernelSecret, 'HS256');

        //set token in user and add token time
        $user->setToken($jwt);
        $user->setTokenTime(new \DateTime('+ 60 Minutes'));
        $this->entityManager->flush();

        $email = $user->getEmail() ?? '';

        //id to finde token in frontend
        $tokenId = $email . '-' . (new \DateTime())->format('Y-m-d_H-i-s');

        //send token to frontend
        $content = $this->app->sendTokenId($tokenId, $jwt);

        //was generated successfully
        if (!isset($content['generated'])) {
            return $this->json([
                'success' => false,
            ]);
        }

        //if everything went right return tokenId and success
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

        $user = $this->userRepository->findOneBy(['token' => $token]);

        if (!$user instanceof User) {
            return $this->json([
                'logout' => false,
                'error' => 'user not logged in',
            ]);
        }

        /** @var string $email */
        $email = $user->getEmail();

        $user->setToken($email); //So that it only can be via email, because else it findes first user with empty token
        $user->setTokenTime(null);
        $this->entityManager->flush();

        return $this->json([
            'logout' => true,
        ]);
    }
}
