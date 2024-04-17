<?php

namespace App\Form;

use App\Entity\Exercices;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;

class ExercicesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', null, [
            'attr' => ['class' => 'form-control mb-3'],
            'constraints' => [
                new NotBlank(['message' => 'nom est obligatoire']),
              
            ],
        ])


        ->add('description', null, [
            'attr' => ['class' => 'form-control mb-3'],
            'constraints' => [
                new NotBlank(['message' => 'description est obligatoire']),
              
            ],
        ])
          
        ->add('image', FileType::class, [
            'label' => 'Profile picture (JPEG, PNG or GIF file)',
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG or GIF).',
                ]),
                new Image([
                    'maxSize' => '1024k',
                    'maxSizeMessage' => 'The file is too large ({{ size }} {{ suffix }}). Maximum allowed size is {{ limit }} {{ suffix }}.',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG or GIF).',
                    'disallowEmptyMessage' => 'Please upload a valid image file (JPEG, PNG or GIF).',
                ]),
            ],
        ])

                 

        ->add('nbr_rep', null, [
            'attr' => ['class' => 'form-control mb-3'],
            'constraints' => [
                new NotBlank(['message' => 'description est obligatoire']),
              
            ],
        ])
         
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exercices::class,
        ]);
    }
}
