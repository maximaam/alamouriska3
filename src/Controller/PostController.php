<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Service\FileUploader;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{File\UploadedFile, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager as ImagineCacheManager;
use Liip\ImagineBundle\Controller\ImagineController;
use function getimagesize;

#[Route(name: 'post_')]
final class PostController extends AbstractController
{
    public const IMG_DIR = '/images/posts/';
    public const IMG_CACHE_DIR = '/public/media/cache/post_image/images/posts/';

    public function __construct(
        private TranslatorInterface $translator,
    ){}

    #[Route('/{type}', name: 'index', requirements: ['type'=>'%seo_route_post_type%'], methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }

    #[Route('/app/post/new', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, ImagineController $imagineController, FileUploader $fileUploader): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            if (null !== $imageFile = $form['image']['imageFile']->getData()) {
                $filename = $fileUploader->upload($imageFile, $this->getParameter('kernel.project_dir').'/public/images/posts');
                $post->getImage()?->setName($filename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);

            if (null !== $post->getImage()) {
                $imagineController->filterAction($request, self::IMG_DIR.$post->getImage()?->getName(), 'post_image');
                $cachedImageSize = getimagesize($this->getParameter('kernel.project_dir').self::IMG_CACHE_DIR.$post->getImage()?->getName());
                $post->getImage()
                    ?->setWidth($cachedImageSize[0])
                    ->setHeight($cachedImageSize[1])
                    ->setMime($cachedImageSize['mime']);
            }

            $entityManager->flush();
            $this->addFlash('success', $this->translator->trans('flash.post_created_confirmation'));

            return $this->redirectToRoute('app_index');
        }

        return $this->render('post/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/app/post/delete/{id}', name: 'delete', methods: ['GET'])]
    public function delete(Post $post, ImagineCacheManager $imagineCacheManager, ): Response
    {
        //$cachePath = $imagineCacheManager->getBrowserPath(self::IMG_DIR, 'post_image');


        //$dir = $this->getParameter('kernel.project_dir').'/media/cache/post_image/images/posts';
        //dd(file_exists($this->getCacheImagePath($post->getImage()?->getName())));
        //$imagineCacheManager->remove(null, 'post_image');


        try {
            $imagePath = $this->getCacheImagePath($post->getImage()?->getName());
            $this->getDoctrine()->getManager()->remove($post);
            $this->getDoctrine()->getManager()->flush();
            unlink($imagePath);
            unlink($this->getParameter('kernel.project_dir').'/public/images/posts/'.$post->getImage()?->getName());
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return $this->redirectToRoute('app_index');
    }

    private function getCacheImagePath(?string $filename): string
    {
        return $this->getParameter('kernel.project_dir').self::IMG_CACHE_DIR.$filename;

    }

}
