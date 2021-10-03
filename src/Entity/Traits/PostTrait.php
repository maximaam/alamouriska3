<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

trait PostTrait
{
    public static function getTypes(bool $keysOnly = true): array
    {
        $data = [
            self::TYPE_WORD => 'word',
            self::TYPE_EXPRESSION => 'expression',
            self::TYPE_PROVERB => 'proverb',
            self::TYPE_JOKE => 'joke',
        ];

        return $keysOnly ? array_keys($data) : $data;
    }

    public function getUploadStoragePath(): string
    {
        return self::IMAGE_PATH;
    }

    /**
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $executionContext): void
    {
        if (self::TYPE_WORD === $this->getType() && true === str_contains(trim($this->getTitle()), ' ')) {
            $executionContext->addViolation('Please add the image for the feature option.');
        }
    }
}
