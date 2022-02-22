<?php

declare(strict_types=1);

namespace App\Component\UserQuestion\Communication;

use App\Component\User\Business\FacadeUserInterface;
use App\Component\UserQuestion\Business\FacadeUserQuestionInterface;
use App\Component\UserQuestion\Persistence\EntityManager\UserQuestionEntityManagerInterface;
use App\Component\UserQuestion\Persistence\Repository\UserQuestionRepositoryInterface;
use App\Controller\CustomAbstractController;
use App\DataProvider\UserQuestionDataProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @psalm-suppress PropertyNotSetInConstructor */
class UserQuestionController extends CustomAbstractController
{
    public function __construct(
        private UserQuestionEntityManagerInterface $userQuestionEntityManager,
        private UserQuestionRepositoryInterface $userQuestionRepository,
        private FacadeUserQuestionInterface $facadeUserQuestion,
        FacadeUserInterface                        $facadeUser,
    ) {
        parent::__construct($facadeUser);
    }

    #[Route("/api/userQuestion/findByExamAndQuestionSlug", name: "api_userQuestion_findByExamAndQuestionSlug", methods: "GET")]
    public function findByExamAndQuestionSlug(Request $request): JsonResponse
    {
        $content = $this->getContent($request);

        /** @var string $token */
        $token = $content['token'] ?? '';
        /** @var string $examSlug */
        $examSlug = $content['examSlug'] ?? '';
        /** @var string $questionSlug */
        $questionSlug = $content['questionSlug'] ?? '';

        $userDataProvider = $this->authenticate($token, 'ROLE_ADMIN');

        if (!$userDataProvider) {
            return $this->json([
                'message' => 'access not authorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $userQuestionList = $this->facadeUserQuestion->findByExamAndQuestionSlugMappedAsArray($examSlug, $questionSlug);

        return $this->json([
            'userQuestionList' => $userQuestionList,
        ]);
    }

    #[Route("/api/userQuestion/updateAnswer", name: "api_userQuestion_updateAnswer", methods: "POST")]
    public function updateAnswer(Request $request): JsonResponse
    {
        $content = $this->getContent($request);

        /** @var string $token */
        $token = $content['token'] ?? '';
        /** @var array $answers */
        $answers = $content['answers'] ?? [];
        /** @var string $question */
        $question = $content['question'] ?? '';

        $userDataProvider = $this->authenticate($token);

        if (!$userDataProvider) {
            return $this->json([
                'message' => 'access not authorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $userQuestionDataProvider = $this->userQuestionRepository->findOneByQuestionAndUser($question, $userDataProvider->getId());

        if(!$userQuestionDataProvider instanceof UserQuestionDataProvider) {
            return $this->json([
                'error' => 'UserQuestion not found',
            ]);
        }

        $userQuestionDataProvider
            ->setUserId($userDataProvider->getId())
            ->setAnswers($answers);

        $this->userQuestionEntityManager->updateAnswer($userQuestionDataProvider);

        return $this->json([
            'updated' => true,
        ]);
    }

    #[Route("/api/userQuestion/deleteByUser", name: "api_userQuestion_deleteByUser", methods: "POST")]
    public function deleteByUser(Request $request): JsonResponse
    {
        $content = $this->getContent($request);

        /** @var string $token */
        $token = $content['token'] ?? '';

        $userDataProvider = $this->authenticate($token);

        if (!$userDataProvider) {
            return $this->json([
                'message' => 'access not authorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $this->userQuestionEntityManager->deleteByUser($userDataProvider->getId());

        return $this->json([
            'deleted' => true,
        ]);
    }
}
