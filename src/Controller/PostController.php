<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Service\FileUploader;
use Exception;
use Liip\ImagineBundle\Imagine\Cache\CacheManager as ImagineCacheManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{File\UploadedFile, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Liip\ImagineBundle\Controller\ImagineController;
use function getimagesize;

#[Route(name: 'app_post_')]
final class PostController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator,
    ){}

    #[Route('/app/post/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, ImagineController $imagineController, FileUploader $fileUploader): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUser($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);

            /** @var UploadedFile $imageFile */
            if (null !== $imageFile = $form['image']['imageFile']->getData()) {
                $filename = $fileUploader->upload($imageFile, $this->getImageDirectoryPath());
                $response = $imagineController->filterAction($request, Post::IMAGE_PATH.$filename, 'post_image');
                $cachedImageSize = getimagesize($this->getCachedImagePath($response->getTargetUrl()));
                $post->getImage()
                    ?->setName($filename)
                    ->setWidth($cachedImageSize[0])
                    ->setHeight($cachedImageSize[1])
                    ->setMime($cachedImageSize['mime']);
            }

            $entityManager->flush();
            $this->addFlash('success', $this->translator->trans('flash.post_created_confirmation'));

            return $this->redirectToRoute('app_index_index');
        }

        return $this->render('post/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{type}', name: 'show', requirements: ['type'=>'%seo_route_post_type%'], methods: ['GET'])]
    public function show(): Response
    {
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }

    #[Route('/app/post/delete/{id}', name: 'delete', methods: ['GET'])]
    public function delete(Post $post, ImagineCacheManager $cacheManager): Response
    {
        try {
            $cacheManager->remove(Post::IMAGE_PATH.$post->getImage()?->getName());
            $this->getDoctrine()->getManager()->remove($post);
            $this->getDoctrine()->getManager()->flush();
            unlink($this->getImageDirectoryPath().$post->getImage()?->getName());
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return $this->redirectToRoute('app_index_index');
    }

    private function getCachedImagePath(?string $url): string
    {
        return $this->getParameter('kernel.project_dir').'/public'.parse_url($url, PHP_URL_PATH);

    }

    private function getImageDirectoryPath(): string
    {
        return $this->getParameter('kernel.project_dir').'/public'.Post::IMAGE_PATH;
    }

}
