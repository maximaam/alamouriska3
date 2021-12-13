<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use App\Entity\Image;
use Generator;
use ReflectionClass;

trait ImageUploadTrait
{
    public function hasGlobalStoragePath(): bool
    {
        return method_exists(self::class, 'getUploadStoragePath');
    }

    public function getImagesProperties(): ?Generator
    {
        $entity = new ReflectionClass(self::class);
        foreach ($entity->getProperties() as $property) {
            if (Image::class === $property->getType()?->getName()) {
                yield $property->getName();
            }
        }

        return null;
    }
}
