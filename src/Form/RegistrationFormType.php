<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class RegistrationFormType extends AbstractType
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
            ->add('plainPassword', PasswordType::class, [
                'trim' => true,
                'label' => 'label.password',
                'help' => 'label.password_constraints',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'password_required',
                    ]),
                    new Length([
                        'min' => 6,
                        'max' => 64,
                        'minMessage' => 'password_min_length',
                        'maxMessage' => 'password_max_length',
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'label.send',
                'row_attr' => [
                    'class' => 'text-end',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
