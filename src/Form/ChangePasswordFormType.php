<?php
declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ChangePasswordFormType
 * @package App\Form
 */
class ChangePasswordFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
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
                    'label' => 'label.new_password',
                ],
                'second_options' => [
                    'label' => 'label.repeat_password',
                ],
                'invalid_message' => 'passwords_mismatch',
                'mapped' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'label.send',
                'row_attr' => [
                    'class' => 'text-end',
                ],
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
