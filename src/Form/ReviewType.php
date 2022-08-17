<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ReviewType extends AbstractType
{
    /**
     * @link https://symfony.com/doc/5.4/reference/forms/types.html
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        // https://symfony.com/doc/5.4/reference/forms/types/form.html#label
            ->add('username', TextType::class, 
            [
                "label" => "Votre Pseudo :",
                "attr" => [
                    "placeholder" => "saisissez votre pseudo ..."
                ]
            ])
            ->add('email', EmailType::class, 
            [
                "label" => "Votre E-Mail :",
                "attr" => [
                    "placeholder" => "saisissez votre email ..."
                ]
            ])
            ->add('content', TextareaType::class, 
            [
                "label" => "Critique :",
            ])
            // https://symfony.com/doc/5.4/reference/forms/types/choice.html#example-usage
            ->add('rating', ChoiceType::class, [
                'placeholder' => 'Votre appréciation...',
                // Si on veut masquer le label (car on a un placeholder)
                "label" => false,
                
                'choices'  => [
                    'Excellent' => 5,
                    'Très bon' => 4,
                    'Correct' => 3,
                    'Bof, peut mieux faire' => 2,
                    'Navet' => 1,
                ],
                // https://symfony.com/doc/5.4/reference/forms/types/choice.html#preferred-choices
                'preferred_choices' => [3, 1]
            ])
            // https://symfony.com/doc/5.4/reference/forms/types/choice.html#multiple
            // https://symfony.com/doc/5.4/reference/forms/types/choice.html#expanded
            ->add('reactions', ChoiceType::class, [
                "label" => "Ce film vous a fait :",
                'choices'  => [
                    '😭' => "cry",
                    '😊' => "smile",
                    '🤔' => "think",
                    '💭' => "dream",
                    '😴' => "sleep",
                ],
                "multiple" => true,
                "expanded" => true
            ])
            // https://symfony.com/doc/5.4/reference/forms/types/date.html
            
            ->add('watchedAt', DateType::class, 
            [
                "widget" => "single_text",
                "label" => "Vous avez vu ce film le :",
                'input'  => 'datetime_immutable'
            ])
            // on a pas besoin de movie car on fera toujours un review sur un film
            // donc déjà sélectionné
            //->add('movie')
            ->add('sauvegarder', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
