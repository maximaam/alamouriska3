<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EditUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'trim' => true,
                'label' => 'label.email_address',
                'help' => 'label.email_private',
            ])
            ->add('displayName', null, [
                'trim' => true,
                'label' => 'label.display_name',
                'help' => 'label.display_name_only_alnum',
            ])
            ->add('avatar', ImageType::class, [
                'label' => 'post.form.label.image',
                'required' => false,
                'help' => '<img src="#" alt="" class="image-preview">',
                'help_html' => true,
                'attr' => [
                    'accept' => 'image/jpg, image/jpeg, image/png',
                    'data-action' => 'change->post-create#imagePreview',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'label.send',
                'row_attr' => [
                    'class' => 'text-end',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
