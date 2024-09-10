<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

class ArticleFormType extends AbstractType
{

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Article|null $article */
        $article = $options['data'] ?? null;

        $cannotEditAuthor = $article && $article->getId() && $article->isPublished();

        $imageConstrains = [
            new Image([
                'maxSize' => '1M',
            ]),
        ];

        if (! $article || ! $article->getImageFilename()) {
            $imageConstrains[] = new NotNull([
                'message' => 'Не выбрано изображение статьи',
            ]);
        }

        $builder
            ->add('title', TextType::class, [
                'label' => 'Укажите название статьи',
                'help' => 'Не используйте в названии слово "собака"',
            ])
            ->add('body', null, [
                'rows' => 15,
            ])
            ->add('author', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return sprintf('%s (id: %d)', $user->getFirstName(), $user->getId());
                },
                'placeholder' => 'Выберите автора статьи',
                'disabled' => $cannotEditAuthor,
            ])
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => $imageConstrains,
            ])
        ;

        if ($options['enable_published_at']) {
            $builder
                ->add('publishedAt', null, [
                    'widget' => 'single_text',
                ])
            ;
        }

        $builder->get('body')
            ->addModelTransformer(new CallbackTransformer(
                function ($bodyFromDatabase) {
                    return str_replace('**собака**', 'собака', $bodyFromDatabase);
                },
                function ($bodyFromInput) {
                    return str_replace('собака', '**собака**', $bodyFromInput);
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'enable_published_at' => false,
        ]);
    }
}
