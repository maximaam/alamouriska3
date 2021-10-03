<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Service\FileUploader;
use Exception;
use Liip\ImagineBundle\Imagine\Cache\CacheManager as ImagineCacheManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Liip\ImagineBundle\Controller\ImagineController;

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
            if ($post->hasImages()) {
                $globalStoragePath = $post->hasGlobalStoragePath()
                    ? $post->getUploadStoragePath()
                    : null;

                foreach ($post::$images as $image) {
                    $imageGetter = sprintf('get%s', ucfirst($image));
                    if (null === $post->$imageGetter()) {
                        continue;
                    }

                    $storagePath = $globalStoragePath ?? sprintf('get%sStoragePath', ucfirst($image));
                    $cacheManager->remove($storagePath.$post->$imageGetter()->getName());
                    @unlink($this->getParameter('public_dir').$storagePath.$post->$imageGetter()->getName());
                }
            }

            $this->getDoctrine()->getManager()->remove($post);
            $this->getDoctrine()->getManager()->flush();
        } catch (Exception $e) {
            dd($e->getMessage());
        }

        return $this->redirectToRoute('app_index_index');
    }

}
