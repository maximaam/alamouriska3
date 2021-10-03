<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Post;
use App\EventSubscriber\FormPostSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function array_walk, sprintf, array_flip, str_replace;

final class PostType extends AbstractType
{
    public function __construct(private FormPostSubscriber $formPostSubscriber) {}

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
                'help_html' => true,
                'help' => (static function () {
                    $links = '<i class="fa fa-link fs-6"></i> <a href="https://www.lexilogos.com/clavier/tamazight_latin.htm" target="_blank">Clavier Tamaziɣt Latin</a>';
                    $links .= ' | ';
                    $links .= '<a href="https://www.lexilogos.com/clavier/tamazight.htm" target="_blank">Clavier Tamaziɣt ⵜⴰⵎⴰⵣⵉⵖⵜ</a>';
                    $links .= ' | ';
                    $links .= '<a href="https://www.lexilogos.com/clavier/araby.htm" target="_blank">Clavier Arabe</a>';
                    return $links;
                })(),
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
            ]);

            foreach (Post::$images as $image) {
                $builder->add($image, ImageType::class, [
                    'required' => false,
                ]);
            }

            $builder->add('submit', SubmitType::class, [
                'label' => 'label.send',
                'row_attr' => [
                    'class' => 'text-end',
                ],
            ])
            ->addEventSubscriber($this->formPostSubscriber);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
