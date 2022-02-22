<?php

declare(strict_types=1);

namespace App\DataProvider;

/**
 * Auto generated data provider
 */
final class QuestionDataProvider
{
    /** @var string */
    protected $question;

    /** @var array */
    protected $rightAnswers;

    /** @var array */
    protected $answers;

    /** @var string */
    protected $slug;

    /**
     * @return string
     */
    public function getQuestion(): string
    {
        return $this->question;
    }


    /**
     * @param string $question
     * @return QuestionDataProvider
     */
    public function setQuestion(string $question)
    {
        $this->question = $question;

        return $this;
    }


    /**
     * @return QuestionDataProvider
     */
    public function unsetQuestion()
    {
        $this->question = null;

        return $this;
    }


    /**
     * @return bool
     */
    public function hasQuestion()
    {
        return ($this->question !== null && $this->question !== []);
    }


    /**
     * @return array
     */
    public function getRightAnswers(): array
    {
        return $this->rightAnswers;
    }


    /**
     * @param array $rightAnswers
     * @return QuestionDataProvider
     */
    public function setRightAnswers(array $rightAnswers)
    {
        $this->rightAnswers = $rightAnswers;

        return $this;
    }


    /**
     * @return QuestionDataProvider
     */
    public function unsetRightAnswer()
    {
        $this->rightAnswers = null;

        return $this;
    }


    /**
     * @return bool
     */
    public function hasRightAnswer()
    {
        return ($this->rightAnswers !== null && $this->rightAnswers !== []);
    }


    /**
     * @return array
     */
    public function getAnswers(): array
    {
        return $this->answers;
    }


    /**
     * @param array $answers
     * @return QuestionDataProvider
     */
    public function setAnswers(array $answers)
    {
        $this->answers = $answers;

        return $this;
    }


    /**
     * @return QuestionDataProvider
     */
    public function unsetAnswers()
    {
        $this->answers = null;

        return $this;
    }


    /**
     * @return bool
     */
    public function hasAnswers()
    {
        return ($this->answers !== null && $this->answers !== []);
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }


    /**
     * @param string $slug
     * @return QuestionDataProvider
     */
    public function setSlug(string $slug)
    {
        $this->slug = $slug;

        return $this;
    }


    /**
     * @return QuestionDataProvider
     */
    public function unsetSlug()
    {
        $this->slug = null;

        return $this;
    }


    /**
     * @return bool
     */
    public function hasSlug()
    {
        return ($this->slug !== null && $this->slug !== []);
    }

    /**
     * @return array
     */
    protected function getElements(): array
    {
        return array(
            'question' =>
                array(
                    'name' => 'question',
                    'allownull' => false,
                    'default' => '',
                    'type' => 'string',
                    'is_collection' => false,
                    'is_dataprovider' => false,
                    'isCamelCase' => false,
                ),
            'rightQuestions' =>
                array(
                    'name' => 'rightQuestions',
                    'allownull' => false,
                    'default' => '',
                    'type' => 'array',
                    'is_collection' => false,
                    'is_dataprovider' => false,
                    'isCamelCase' => false,
                ),
            'answers' =>
                array(
                    'name' => 'answers',
                    'allownull' => false,
                    'default' => '',
                    'type' => 'array',
                    'is_collection' => false,
                    'is_dataprovider' => false,
                    'isCamelCase' => false,
                ),
            'slug' =>
                array(
                    'name' => 'slug',
                    'allownull' => false,
                    'default' => '',
                    'type' => 'string',
                    'is_collection' => false,
                    'is_dataprovider' => false,
                    'isCamelCase' => false,
                ),
        );
    }
}
