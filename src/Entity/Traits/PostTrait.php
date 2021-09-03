<?php

declare(strict_types=1);

namespace App\Entity\Traits;

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
}
