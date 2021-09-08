<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\FileUploader;
use Liip\ImagineBundle\Controller\ImagineController;
use LogicException;
use App\Form\EditUserFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(name: 'app_')]
final class SecurityController extends AbstractController
{
    public const IMG_DIR = '/images/avatars/';
    public const IMG_CACHE_DIR = '/public/media/cache/avatar_image/images/avatars/';

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_index_index');
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/profile', name: 'profile_show')]
    public function profile(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('security/profile_show.html.twig');
    }

    #[Route('/profile/edit', name: 'profile_edit')]
    public function profileEdit(Request $request, TranslatorInterface $translator, ImagineController $imagineController, FileUploader $fileUploader): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(EditUserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            if (null !== $imageFile = $form['avatar']['imageFile']->getData()) {
                $filename = $fileUploader->upload($imageFile, $this->getParameter('kernel.project_dir').'/public/images/avatars', $user->getDisplayName());

                $imagineController->filterAction($request, self::IMG_DIR.$filename, 'avatar_image_128');
                $imagineController->filterAction($request, self::IMG_DIR.$filename, 'avatar_image_64');
                $imagineController->filterAction($request, self::IMG_DIR.$filename, 'avatar_image_32');
                $user->getAvatar()
                    ?->setName($filename)
                    ->setWidth(128)
                    ->setHeight(128)
                    ->setMime('image/webp');
            }

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', $translator->trans('flash.profile_edit_success'));

            return $this->redirectToRoute('app_profile_show');
        }

        return $this->render('security/profile_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
