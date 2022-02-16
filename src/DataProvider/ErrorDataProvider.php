<?php

declare(strict_types=1);

namespace App\DataProvider;

/**
 * Auto generated data provider
 */
final class ErrorDataProvider
{
    protected ?array $errors = [];

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setErrors(array $errors): ErrorDataProvider
    {
        $this->errors = $errors;

        return $this;
    }

    public function unsetErrors(): ErrorDataProvider
    {
        $this->errors = null;

        return $this;
    }

    public function hasErrors(): bool
    {
        return ($this->errors !== null && $this->errors !== []);
    }

    public function addError(string $Error): ErrorDataProvider
    {
        $this->errors[] = $Error;
        return $this;
    }

    protected function getElements(): array
    {
        return [
            'errors' =>
                [
                    'name' => 'errors',
                    'allownull' => false,
                    'default' => '',
                    'type' => 'array',
                    'is_collection' => false,
                    'is_dataprovider' => false,
                    'isCamelCase' => false,
                    'singleton' => 'Error',
                    'singleton_type' => 'array',
                ],
        ];
    }
}
