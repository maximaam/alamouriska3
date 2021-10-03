<?php

declare(strict_types=1);

namespace App\Entity\Traits;

trait ImageUploadTrait
{
    public function hasImages(): bool
    {
        return property_exists(self::class, 'images');
    }

    public function hasGlobalStoragePath(): bool
    {
        return method_exists(self::class, 'getUploadStoragePath');
    }

}
