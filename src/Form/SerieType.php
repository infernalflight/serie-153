<?php

namespace App\Form;

use App\Entity\Serie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class SerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la série',
                'required' => false,
                'attr' => [
                    'class' => 'class-special'
                ]
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
            ->add('poster_file', FileType::class, [
                'mapped' => false,
                'label' => 'Fichier poster',
                'required' => false,
                'constraints' => [
                    New File([
                       'maxSize'=> '1024k',
                       'maxSizeMessage' => 'Ton image est trop lourde. Max : 1Mo',
                       'mimeTypes' => [
                           'image/jpeg',
                           'image/jpg',
                           'image/png',
                       ],
                        'mimeTypesMessage'  => 'Le format n\'est pas pris en charge',
                    ]),
                ]
            ])
            ->add('firstAirDate', null, [
                'widget' => 'single_text',
            ])
            ->add('lastAirDate', null, [
                'widget' => 'single_text',
                'required' => false,
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
