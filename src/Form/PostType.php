<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function array_walk, sprintf, array_flip, str_replace;

final class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $types = Post::getTypes(false);
        array_walk($types, static function (string &$val) {
            $val = sprintf('post.%s.singular', $val);
        });

        $builder
            ->add('type', ChoiceType::class, [
                'placeholder' => 'post.form.label.type',
                'label' => false,
                'choices' => array_flip($types),
                'choice_attr' => static function ($choice, $key, $value) {
                    return ['data-label' => str_replace('singular', 'title_hint', $key)];
                },
                'attr' => [
                    'data-action' => 'change->post-create#typeChange',
                ],
            ])
            ->add('title', null, [
                'label' => 'post.form.label.title',
                'attr' => [
                    'rows' => 3,
                    'class' => 'post-title',
                ]
            ])
            ->add('description', null, [
                'label' => 'post.form.label.description',
                'attr' => [
                    'rows' => 3,
                ]
            ])
            ->add('isQuestion', null, [
                'label' => 'post.form.label.question',
            ])
            ->add('image', ImageType::class, [
                'label' => 'post.form.label.image',
                'required' => false,
                'help' => '<img src="#" alt="" class="image-preview">',
                'help_html' => true,
                'attr' => [
                    'accept' => 'image/jpg, image/jpeg, image/png',
                    'data-action' => 'change->share#imagePreview',
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
            'data_class' => Post::class,
        ]);
    }
}
