<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Validator\Constraints\Image as ImageConstraint;
use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'input-image',
                    'accept' => 'image/jpg, image/jpeg, image/png',
                    'data-action' => 'change->share#imagePreview',
                ],
                'help' => 'post.form.label.image.help',
                'constraints' => [
                    new ImageConstraint([
                        'maxSize' => '2M',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
