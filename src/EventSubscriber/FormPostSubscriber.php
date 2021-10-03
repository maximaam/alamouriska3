<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Service\FileUploader;
use JetBrains\PhpStorm\ArrayShape;
use Liip\ImagineBundle\Controller\ImagineController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class FormPostSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private FileUploader $fileUploader,
        private ImagineController $imagineController,
        private RequestStack $requestStack,
        private ParameterBagInterface $parameterBag,
    ) {}

    #[ArrayShape([FormEvents::POST_SUBMIT => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        ];
    }

    public function onPostSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        $entity = $event->getData();

        if ($entity->hasImages()) {
            $this->uploadImages($form, $entity);
        }

        //do other stuff
    }

    private function uploadImages(FormInterface $form, mixed $entity): void
    {
        foreach ($entity::$images as $image) {
            if (null === $uploadedImage = $form[$image]['imageFile']->getData()) {
                continue;
            }

            $imageGetter = sprintf('get%s', ucfirst($image));
            $imageGetterStorage = sprintf('get%sStoragePath', ucfirst($image));
            $storagePath = $entity->hasGlobalStoragePath()
                ? $entity->getUploadStoragePath()
                : $entity->$imageGetterStorage();

            $filename = $this->fileUploader->upload($uploadedImage, $this->parameterBag->get('public_dir').$storagePath);
            $filterResponse = $this->imagineController->filterAction($this->requestStack->getCurrentRequest(), $storagePath.$filename, 'post_image');

            $cachedImagePath = $this->parameterBag->get('public_dir').parse_url($filterResponse->getTargetUrl(), PHP_URL_PATH);
            $cachedImageSize = getimagesize($cachedImagePath);

            $entity->$imageGetter()
                ?->setName($filename)
                ->setWidth($cachedImageSize[0])
                ->setHeight($cachedImageSize[1])
                ->setMime($cachedImageSize['mime']);
        }
    }
}