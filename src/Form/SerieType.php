<?php

namespace App\Form;

use App\Entity\Serie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la série',
                'required' => false,
            ])
            ->add('overview')
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'returning' => 'returning',
                    'ended' => 'ended',
                    'canceled' => 'canceled',
                ],
                'placeholder' => ' -- Veuillez sélectionner un statut --',
                'expanded' => true,
            ])
            ->add('vote')
            ->add('popularity')
            ->add('genres')
            ->add('backdrop')
            ->add('tmdbId')
            ->add('dateCreated', null, [
                'widget' => 'single_text',
            ])
            ->add('dateModified', null, [
                'widget' => 'single_text',
            ])
            ->add('poster')
            ->add('firstAirDate', null, [
                'widget' => 'choice',
            ])
            ->add('lastAirDate', null, [
                'widget' => 'single_text',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'OK'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Serie::class,
        ]);
    }
}
