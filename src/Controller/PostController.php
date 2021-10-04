<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Service\UploadedFileRemover;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(name: 'app_post_')]
final class PostController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator,
    ){}

    #[Route('/app/post/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
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
    public function delete(Post $post, UploadedFileRemover $fileRemover): Response
    {
        $fileRemover->remove($post);
        $this->getDoctrine()->getManager()->remove($post);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('app_index_index');
    }

}
