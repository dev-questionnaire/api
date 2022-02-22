<?php

declare(strict_types=1);

namespace App\DataProvider;

/**
 * Auto generated data provider
 */
final class ExamDataProvider
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $slug;

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }


    /**
     * @param string $name
     * @return ExamDataProvider
     */
    public function setName(?string $name = null)
    {
        $this->name = $name;

        return $this;
    }


    /**
     * @return ExamDataProvider
     */
    public function unsetName()
    {
        $this->name = null;

        return $this;
    }


    /**
     * @return bool
     */
    public function hasName()
    {
        return ($this->name !== null && $this->name !== []);
    }

    /**
     * @return string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }


    /**
     * @param string $slug
     * @return ExamDataProvider
     */
    public function setSlug(?string $slug = null)
    {
        $this->slug = $slug;

        return $this;
    }


    /**
     * @return ExamDataProvider
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
        return [
            'name' =>
                [
                    'name' => 'name',
                    'allownull' => false,
                    'default' => '',
                    'type' => 'string',
                    'is_collection' => false,
                    'is_dataprovider' => false,
                    'isCamelCase' => false,
                ],
            'slug' =>
                [
                    'name' => 'slug',
                    'allownull' => false,
                    'default' => '',
                    'type' => 'string',
                    'is_collection' => false,
                    'is_dataprovider' => false,
                    'isCamelCase' => false,
                ]
        ];
    }
}
