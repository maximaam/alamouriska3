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

/**
 * Class PostType
 * @package App\Form
 */
final class PostType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
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
                'choice_attr' => function ($choice, $key, $value) {
                    return ['data-label' => str_replace('singular', 'title_hint', $key)];
                },
            ])
            ->add('title', null, [
                'label_attr' => [
                    'class' => 'd-none',
                ],
                'attr' => [
                    'placeholder' => 'post.form.label.title',
                ]
            ])
            ->add('description', null, [
                'label_attr' => [
                    'class' => 'd-none',
                ],
                'attr' => [
                    'placeholder' => 'post.form.label.description',
                    'rows' => 5,
                ]
            ])
            ->add('isQuestion', null, [
                'label' => 'post.form.label.question',
            ])
            ->add('image', ImageType::class, [
                'label' => 'post.form.label.image',
                'required' => false,
                'help' => '<img src="#" alt="" class="img-preview">',
                'help_html' => true,
                'attr' => [
                    'accept' => 'image/jpeg, image/png',
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

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
