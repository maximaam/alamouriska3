<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PostController
 * @package App\Controller
 */
final class PostController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator,
    ){}

    #[Route('/app/creer-publication', name: 'post_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', $this->translator->trans('flash.post_created_confirmation'));

            return $this->redirectToRoute('app_index');
        }

        return $this->render('post/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{type}', name: 'post_index', requirements: ['type'=>'%seo_route_post_type%'], methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }
}
