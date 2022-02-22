<?php

namespace App\Component\Exam\Communication;

use App\Component\Exam\Business\FacadeExamInterface;
use App\Component\User\Business\FacadeUserInterface;
use App\Component\Exam\Dependency\BridgeQuestionInterface;
use App\Component\Exam\Persistence\Repository\ExamRepositoryInterface;
use App\Component\Exam\Dependency\BridgeUserQuestionInterface;
use App\Controller\CustomAbstractController;
use App\DataProvider\UserDataProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @psalm-suppress PropertyNotSetInConstructor */
class ExamController extends CustomAbstractController
{
    public function __construct(
        private ExamRepositoryInterface     $examRepository,
        private BridgeQuestionInterface     $bridgeQuestion,
        private BridgeUserQuestionInterface $bridgeUserQuestion,
        private FacadeExamInterface         $facadeExam,
        FacadeUserInterface                 $userFacade,
    )
    {
        parent::__construct($userFacade);
    }

    #[Route("/api/exam/getAll", name: "api_exam_getAll", methods: "GET")]
    public function getAll(Request $request): JsonResponse
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

        $examListFormattedAsArray = $this->facadeExam->getAllFormattedAsArray();

        return $this->json([
            'examList' => $examListFormattedAsArray,
        ]);
    }

    #[Route("/api/exam/findBySlug", name: "api_exam_findBySlug", methods: "GET")]
    public function findBySlug(Request $request): JsonResponse
    {
        $content = $this->getContent($request);

        /** @var string $token */
        $token = $content['token'] ?? '';
        /** @var string $slug */
        $slug = $content['examSlug'] ?? '';

        $authenticate = $this->authenticate($token);

        if (!$authenticate) {
            return $this->json([
                'message' => 'access not authorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $exam = $this->facadeExam->findBySlugFormattedAsArray($slug);

        return $this->json([
            'exam' => $exam,
        ]);
    }

    #[Route("/api/exam/result", name: "api_exam_result", methods: "GET")]
    public function result(Request $request): JsonResponse
    {
        $content = $this->getContent($request);

        /** @var string $token */
        $token = $content['token'] ?? '';
        /** @var string $examSlug */
        $examSlug = $content['examSlug'] ?? '';
        /** @var string $role */
        $role = $content['role'] ?? 'ROLE_USER';

        $adminPage = false;

        $userDataProvider = $this->authenticate($token, $role);

        if (!$userDataProvider instanceof UserDataProvider) {
            return $this->json([
                'message' => 'access not authorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        /** @var int $id */
        $id = $userDataProvider->getId();

        if ($role === 'ROLE_ADMIN') {
            $adminPage = true;
            $id = $content['id'] ?? 0;
        }

        $examDataProvider = $this->examRepository->findBySlug($examSlug);

        if (!$examDataProvider) {
            return $this->json([
                'error' => 'exam not found',
            ]);
        }

        $questionDataProviderList = $this->bridgeQuestion->getByExamSlug($examSlug);

        $userQuestionDataProviderList = $this->bridgeUserQuestion->findByUserAndExamIndexedByQuestionSlug($id, $examSlug);

        $percentAnswerCorrectAndUserAnswerLists = $this->bridgeUserQuestion->getPercentAndAnswerCorrectAndUserAnswerList($questionDataProviderList, $userQuestionDataProviderList, $adminPage);

        if (!array_key_exists('percent', $percentAnswerCorrectAndUserAnswerLists)
            || !array_key_exists('answeredCorrect', $percentAnswerCorrectAndUserAnswerLists)
            || !array_key_exists('userAnswerList', $percentAnswerCorrectAndUserAnswerLists)
        ) {
            return $this->json([
                'error' => 'not answered any questions',
            ]);
        }

        $calculatedPercent = $percentAnswerCorrectAndUserAnswerLists['percent'];

        $answeredCorrect = $percentAnswerCorrectAndUserAnswerLists['answeredCorrect'];

        $userAnswerList = $percentAnswerCorrectAndUserAnswerLists['userAnswerList'];

        if($adminPage === false) {
            foreach ($answeredCorrect as $answer) {
                if ($answer === null) {
                    return $this->json([
                        'error' => 'not all questions were answerd',
                    ]);
                }
            }
        }

        return $this->json([
            'examPercent' => $calculatedPercent,
            'userAnswers' => $userAnswerList,
            'answeredCorrect' => $answeredCorrect,
        ]);
    }
}
