<?php

namespace App\Form;

use App\Entity\Regime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank ;

class RegimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
          

            ->add('petitdej', null, [
                'attr' => ['class' => 'form-control mb-3'],
                'constraints' => [
                    new NotBlank(['message' => 'Petit déjeuner est obligatoire']),
                  
                ],
                'label' => 'Petit déjeuner', // Libellé du champ
            ])


            ->add('colpdej', null, [
                'attr' => ['class' => 'form-control mb-3'],
                'constraints' => [
                    new NotBlank(['message' => 'Snack  est obligatoire']),
                  
                ],
                'label' => 'Snack', // Libellé du champ
            ])


            ->add('dej', null, [
                'attr' => ['class' => 'form-control mb-3'],
                'constraints' => [
                    new NotBlank(['message' => 'Luch  est obligatoire']),
                  
                ],
                'label' => 'Luch', // Libellé du champ
            ])

            ->add('coldej', null, [
                'attr' => ['class' => 'form-control mb-3'],
                'constraints' => [
                    new NotBlank(['message' => 'Snack  est obligatoire']),
                  
                ],
                'label' => 'Snack', // Libellé du champ
            ])
         
            ->add('diner', null, [
                'attr' => ['class' => 'form-control mb-3'],
                'constraints' => [
                    new NotBlank(['message' => 'Dinner  est obligatoire']),
                  
                ],
                'label' => 'Dinner', // Libellé du champ
            ])
           
            
          
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Regime::class,
        ]);
    }
}
