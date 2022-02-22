<?php

namespace App\Component\Question\Communication;

use App\Component\Question\Business\FacadeQuestionInterface;
use App\Component\Question\Persistence\Repository\QuestionRepositoryInterface;
use App\Component\User\Business\FacadeUserInterface;
use App\Controller\CustomAbstractController;
use App\DataProvider\UserDataProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @psalm-suppress PropertyNotSetInConstructor */
class QuestionController extends CustomAbstractController
{
    public function __construct(
        private QuestionRepositoryInterface $questionRepository,
        private FacadeQuestionInterface     $facadeQuestion,
        FacadeUserInterface                 $facadeUser,
    )
    {
        parent::__construct($facadeUser);
    }

    #[Route("/api/question/getCurrentQuestion", name: "api_question_getCurrentQuestion", methods: "GET")]
    public function getCurrentQuestion(Request $request): JsonResponse
    {
        $content = $this->getContent($request);

        /** @var string $token */
        $token = $content['token'] ?? '';
        /** @var string $examSlug */
        $examSlug = $content['examSlug'] ?? '';

        $userDataProvider = $this->authenticate($token);

        if (!$userDataProvider instanceof UserDataProvider) {
            return $this->json([
                'message' => 'access not authorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $questionDataProviderList = $this->questionRepository->findByExamSlug($examSlug);

        /** @var int $id */
        $id = $userDataProvider->getId();

        $currentQuestionDataProvider = $this->facadeQuestion->getCurrentQuestionAndCreateUserQuestionMappedAsArray($questionDataProviderList, $examSlug, $id);

        if (empty($currentQuestionDataProvider)) {
            return $this->json([
                'error' => 'Question not found',
            ]);
        }

        return $this->json([
            'question' => $currentQuestionDataProvider,
        ]);
    }

    #[Route("/api/question/findByExamSlug", name: "api_question_findByExamSlug", methods: "GET")]
    public function findByExamSlug(Request $request): JsonResponse
    {
        $content = $this->getContent($request);

        /** @var string $token */
        $token = $content['token'] ?? '';
        /** @var string $examSlug */
        $examSlug = $content['examSlug'] ?? '';

        $userDataProvider = $this->authenticate($token);

        if (!$userDataProvider) {
            return $this->json([
                'message' => 'access not authorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $questionList = $this->facadeQuestion->findByExamSlugMappedAsArray($examSlug);

        return $this->json([
            'questionList' => $questionList,
        ]);
    }

    #[Route("/api/question/findOneByExamAndQuestionSlug", name: "api_question_findOneByExamAndQuestionSlug", methods: "GET")]
    public function findOneByExamAndQuestionSlug(Request $request): JsonResponse
    {
        $content = $this->getContent($request);

        /** @var string $token */
        $token = $content['token'] ?? '';
        /** @var string $examSlug */
        $examSlug = $content['examSlug'] ?? '';
        /** @var string $questionSlug */
        $questionSlug = $content['questionSlug'] ?? '';

        $userDataProvider = $this->authenticate($token);

        if (!$userDataProvider) {
            return $this->json([
                'message' => 'access not authorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $question = $this->facadeQuestion->findOneByExamAndQuestionSlug($examSlug, $questionSlug);

        return $this->json([
            'question' => $question,
        ]);
    }
}
