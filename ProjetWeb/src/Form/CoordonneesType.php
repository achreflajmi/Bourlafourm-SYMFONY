<?php

namespace App\Form;

use App\Entity\Coordonnees;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;
class CoordonneesType extends AbstractType

{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('sexe', ChoiceType::class, [
            'attr' => ['class' => 'form-control mb-3'],
            'choices' => [
                'Homme' => 'Homme',
                'Femme' => 'Femme',
            ],
            'constraints' => [
                new NotBlank(['message' => 'sexe est obligatoire']),
              
            ],
            'placeholder' => 'Choisir le sexe', 
            'label' => 'Sexe', 
        ])
            ->add('age', null, [
                'attr' => ['class' => 'form-control mb-3'],
                'constraints' => [
                    new NotBlank(['message' => 'age est obligatoire']),
                  
                ],
            ])
            ->add('taille', null, [
                'attr' => ['class' => 'form-control mb-3'],
                'constraints' => [
                    new NotBlank(['message' => 'taille est obligatoire']),
               
                ],
            ])
            ->add('poids', null, [
                'attr' => ['class' => 'form-control mb-3'],
                'constraints' => [
                    new NotBlank(['message' => 'poids est obligatoire']),
                   
                ],
            ])
            ->add('imc', null, [
                'attr' => ['class' => 'form-control mb-3'],
                'constraints' => [
                    new NotBlank(['message' => 'imc est obligatoire']),
                  
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Coordonnees::class,
        ]);
    }
}
