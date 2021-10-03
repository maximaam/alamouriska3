<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Post;
use App\Entity\User;
use App\Service\FileUploader;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Liip\ImagineBundle\Controller\ImagineController;
use Liip\ImagineBundle\Imagine\Cache\CacheManager as ImagineCacheManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use function unlink;

final class EntityLifecycleListener
{
    public function __construct(
        private RequestStack $requestStack,
        private FileUploader $fileUploader,
        private ImagineController $imagineController,
        private ParameterBagInterface $parameterBag,
        private ImagineCacheManager $cacheManager,
    ) {}

    public function okprePersist(Object $entity, LifecycleEventArgs $args): void
    {
        dd($args->getObject()->getImage());
        $this->uploadImage($entity);
    }

    public function okpostRemove(Object $entity, LifecycleEventArgs $args): void
    {
        //$this->cacheManager->remove($cachePath);
        //unlink($uploadPath);
        $args->getObjectManager()->remove($entity);
        $args->getObjectManager()->flush();
    }

    public function okpreUpdate(Object $entity, PreUpdateEventArgs $args): void
    {
        $this->uploadImage($entity);
    }

    private function uploadImage($entity): void
    {
        if (null === $request = $this->requestStack->getCurrentRequest()) {
            return;
        }

        $files = $request->files->getIterator()->getArrayCopy();
        if (empty($files)) {
            return;
        }

        $files = array_shift($files);
        $storagePath = $this->parameterBag->get('kernel.project_dir').'/public'.$entity->getFileStorageDir();

        $filename = $liipFilter = null;
        switch (true) {
            case $entity instanceof User:
                $liipFilter = 'avatar_image';
                $filename = $entity->getDisplayName();
                break;
            case $entity instanceof Post:
                $liipFilter = 'post_image';
                break;
        }

        /** @var UploadedFile $file */
        foreach ($files as $propertyName => $file) {
            if (null === $file['imageFile']) {
                continue;
            }

            $filePropertyGetter = sprintf('get%s', ucfirst($propertyName));
            $uploadFilename = $this->fileUploader->upload($file['imageFile'], $storagePath, $filename);
            $response = $this->imagineController->filterAction($request, $entity->getFileStorageDir().$uploadFilename, $liipFilter);
            $cachedPath = $this->parameterBag->get('kernel.project_dir').'/public'.parse_url($response->getTargetUrl(), PHP_URL_PATH);
            $cachedImageSize = getimagesize($cachedPath);
            $entity->$filePropertyGetter()
                ?->setName($uploadFilename)
                ->setWidth($cachedImageSize[0])
                ->setHeight($cachedImageSize[1])
                ->setMime($cachedImageSize['mime']);
        }
        
    }
}
