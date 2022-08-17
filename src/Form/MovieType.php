<?php

namespace App\Form;

use App\Entity\Genre;
use App\Entity\Movie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('summary')
            ->add('synopsis')
            ->add('releasedAt')
            ->add('duration')
            ->add('poster')
            ->add('country')
            ->add('rating')
            ->add('type')
            // https://symfony.com/doc/current/reference/forms/types/entity.html
            ->add('genres', EntityType::class, 
            [
                'class' => Genre::class,
                // https://symfony.com/doc/current/reference/forms/types/entity.html#choice-label
                // Si j'ai plusieur champs : concatenation à faire
                // je créer une function dans l'entité qui le fait
                // 'choice_label' => 'tagada', // va apeller getTagada()
                'choice_label' => 'name', 
                // If true, the user will be able to select multiple options
                "multiple" => true,
                // https://symfony.com/doc/current/reference/forms/types/entity.html#expanded
                // radio buttons or checkboxes
                "expanded" => true,
                'documentation' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'integer'
                    ],
                    'description' => 'comma separated integer'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
