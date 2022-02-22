<?php
declare(strict_types=1);

namespace App\Component\UserQuestion\Business\Model;

use App\DataProvider\QuestionDataProvider;

class GetPercentAndAnswerCorrectAndUserAnswerList implements GetPercentAndAnswerCorrectAndUserAnswerListInterface
{
    /**
     * @param array<array-key, \App\DataProvider\QuestionDataProvider> $questionDataProviderList
     * @param array<array-key, \App\DataProvider\UserQuestionDataProvider> $userQuestionDataProviderList
     * @param bool $isAdminPage
     * @return array{answeredCorrect?: array<array-key, bool|null>, percent?: float|int, userAnswerList?: array<array-key, array<array-key, null|string>|null>}
     */
    public function get(array $questionDataProviderList, array $userQuestionDataProviderList, bool $isAdminPage = false): array
    {
        $countQuestions = 0;

        /** @var array<array-key, bool|null> $answeredCorrect */
        $answeredCorrect = [];

        /** @var array<array-key, array<array-key, string|null>> $userAnswerList */
        $userAnswerList = [];

        if(empty($userQuestionDataProviderList) && $isAdminPage === false) {
            return [];
        }

        foreach ($questionDataProviderList as $questionDataProvider) {
            $slug = $questionDataProvider->getSlug();
            $userAnswerList[$slug] = null;
            $answeredCorrect[$slug] = null;

            if (!array_key_exists($slug, $userQuestionDataProviderList)) {
                continue;
            }

            /** @var array<array-key, bool>|null $answerList */
            $answerList = $userQuestionDataProviderList[$slug]->getAnswers();

            if ($answerList === null && $isAdminPage === true) {
                continue;
            }

            if ($answerList === null) {
                return [];
            }

            $answeredCorrect[$slug] = null;


            foreach ($answerList as $answer => $result) {
                if (!is_string($answer)) {
                    $answer = (string)$answer;
                }

                if ($result === true) {
                    $userAnswerList[$slug][] = str_replace('_', ' ', $answer);
                }

                $answeredCorrect[$slug] = $this->getCurrentAnsweredCorrect(
                    $questionDataProvider,
                    $answeredCorrect[$slug],
                    $answer,
                    $result
                );
            }

            if ($answeredCorrect[$slug] === true) {
                $countQuestions++;
            }
        }

        $calculatedPercent = 100;

        $questionQuantity = count($questionDataProviderList);

        if ($questionQuantity !== 0) {
            $calculatedPercent = $countQuestions / $questionQuantity * 100;
        }

        return [
            'answeredCorrect' => $answeredCorrect,
            'percent' => $calculatedPercent,
            'userAnswerList' => $userAnswerList,
        ];
    }

    private function getCurrentAnsweredCorrect(QuestionDataProvider $questionDataProvider, bool|null $currentAnsweredCorrect, string $answer, bool $result): bool|null
    {
        $rightAnswers= $questionDataProvider->getRightAnswers();

        /** @var string $rightAnswer */
        foreach ($rightAnswers as $rightAnswer) {
            if ($currentAnsweredCorrect === false) {
                break;
            }

            $rightAnswer = str_replace(' ', '_', $rightAnswer);

            if ($rightAnswer === $answer && $result === true) {
                $currentAnsweredCorrect = true;
                continue;
            }

            if ($rightAnswer === $answer && $result === false) {
                $currentAnsweredCorrect = false;
                continue;
            }

            if ($result === true) {
                $currentAnsweredCorrect = false;
            }
        }

        return $currentAnsweredCorrect;
    }
}