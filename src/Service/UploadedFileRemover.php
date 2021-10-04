<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Post;
use App\Entity\User;
use Liip\ImagineBundle\Imagine\Cache\CacheManager as ImagineCacheManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use function sprintf, ucfirst, unlink;

final class UploadedFileRemover
{
    public function __construct(
        private ImagineCacheManager $cacheManager,
        private ParameterBagInterface $parameterBag,
    ) {}

    public function remove(Post|User $entity): void
    {
        if (null === $imagesProperties = $entity->getImagesProperties()) {
            return;
        }

        $globalStoragePath = $entity->hasGlobalStoragePath()
            ? $entity->getUploadStoragePath()
            : null;

        foreach ($imagesProperties as $imagesProperty) {
            $imageGetter = sprintf('get%s', ucfirst($imagesProperty['name']));
            if (null === $entity->$imageGetter()) {
                continue;
            }

            $storagePath = $globalStoragePath ?? sprintf('get%sStoragePath', ucfirst($imagesProperty['name']));
            $this->cacheManager->remove($storagePath.$entity->$imageGetter()->getName());
            unlink($this->parameterBag->get('public_dir').$storagePath.$entity->$imageGetter()->getName());
        }
    }

}
