<?php
declare(strict_types=1);

namespace App\Entity\Traits;

use JetBrains\PhpStorm\ArrayShape;

/**
 * Trait PostTrait
 * @package App\Entity\Traits
 */
trait PostTrait
{
    /**
     * @param bool $keysOnly
     * @return string[]
     */
    #[ArrayShape([self::TYPE_WORD => "string", self::TYPE_EXPRESSION => "string", self::TYPE_PROVERB => "string", self::TYPE_JOKE => "string", self::TYPE_BLOG => "string"])]
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