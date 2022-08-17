<?php

namespace App\Form;

use App\Entity\Movie;
use App\Entity\Season;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeasonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('nbEpisode')
            ->add('movie', EntityType::class, 
            [
                'class' => Movie::class,
                'choice_label' => 'title',
                'label' => 'la série',
                // on ne met pas 'multiple' ou 'expanded'
                // car leur valeur par défaut est false
                // c'est ce que l'on veux
                // "multiple" => false,
                // "expanded" => false

                // https://symfony.com/doc/current/reference/forms/types/entity.html#using-a-custom-query-for-the-entities
                // on remplace le findAll() par défaut
                // par une requete Kustom
                'query_builder' => function (MovieRepository $er) {
                    
                    // return $er->getAllMovieOrderByTitleForForm();
                    /** autre solution avec un filtre sur le type */

                    return $er->createQueryBuilder('m')
                        ->where('m.type = :type')
                        ->setParameter(':type', "Série");
                },
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Season::class,
        ]);
    }
}
