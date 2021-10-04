<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Post;
use App\Entity\User;
use App\Service\FileUploader;
use JetBrains\PhpStorm\ArrayShape;
use Liip\ImagineBundle\Controller\ImagineController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use function sprintf, ucfirst, getimagesize;

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

        $this->uploadImages($form, $entity);
        //do other stuff
    }

    private function uploadImages(FormInterface $form, Post|User $entity): void
    {
        if (null === $imagesProperties = $entity->getImagesProperties()) {
            return;
        }

        $globalStoragePath = $entity->hasGlobalStoragePath()
            ? $entity->getUploadStoragePath()
            : null;

        foreach ($imagesProperties as $imageProperty) {
            if (null === $uploadedImage = $form[$imageProperty['name']]['imageFile']->getData()) {
                continue;
            }

            $imageGetter = sprintf('get%s', ucfirst($imageProperty['name']));
            $storagePath = $globalStoragePath ?? sprintf('get%sStoragePath', ucfirst($imageProperty['name']));
            $filename = $this->fileUploader->upload($uploadedImage, $this->parameterBag->get('public_dir').$storagePath);
            $filterResponse = $this->imagineController->filterAction($this->requestStack->getCurrentRequest(), $storagePath.$filename, $imageProperty['liip_filter']);
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