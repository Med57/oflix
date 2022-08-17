<?php

namespace App\Form;

use App\Entity\Actor;
use App\Entity\Casting;
use App\Entity\Movie;
use App\Repository\MovieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CastingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('role')
            ->add('creditOrder')
            ->add('movie', EntityType::class, 
            [
                'class' => Movie::class,
                'choice_label' => 'title',
                // on ne met pas 'multiple' ou 'expanded'
                // car leur valeur par défaut est false
                // c'est ce que l'on veux
                // "multiple" => false,
                // "expanded" => false

                // https://symfony.com/doc/current/reference/forms/types/entity.html#using-a-custom-query-for-the-entities
                // on remplace le findAll() par défaut
                // par une requete Kustom
                'query_builder' => function (MovieRepository $er) {
                    return $er->getAllMovieOrderByTitleForForm();
                },
                
            ])
            ->add('actor', EntityType::class, 
            [
                'class' => Actor::class,
                // https://symfony.com/doc/current/reference/forms/types/entity.html#choice-label
                // Si j'ai plusieur champs : concatenation à faire
                // je créer une function dans l'entité qui le fait
                'choice_label' => 'fullname', // va apeller getFullname()
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Casting::class,
        ]);
    }
}
