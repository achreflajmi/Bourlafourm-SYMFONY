<?php

namespace App\Form;

use App\Entity\User;
use App\Service\FileUploader;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\DateType;


use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;




class RegistrationFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
      $roles = ['ROLE_SPORTIF' => 'ROLE_SPORTIF', 
      'ROLE_COATCH' => 'ROLE_COATCH' , 
      'ROLE_NUTRITIONIST' => 'ROLE_NUTRITIONIST'];
      //, 'ROLE_ADMIN' => 'ROLE_ADMIN'
  
        $roles = array_map(function($role) {
            return (string) $role;
        }, $roles);
 $builder
        ->add('name', null, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer un nom',
                ]),
            ],
        ])
        ->add('prenom', null, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer un prénom',
                ]),
            ],
        ])
        ->add('email', null, [
            'constraints' => [
                new Email([
                    'message' => 'The email "{{ value }}" is not a valid email address.',
                ]),
            ],
        ])
        ->add('dateN', DateType::class, [
            'label' => 'Date de naissance',
            'widget' => 'single_text',
            'required' => false,
            'attr' => [
                'class' => 'form-control mb-3',
                'placeholder' => 'Date de naissance',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer une date de naissance',
                ]),
                new Callback([
                    'callback' => function ($object, ExecutionContextInterface $context) {
                        $dateNow = new \DateTime();
                        $age = $dateNow->diff($object)->y;
                        
                        if ($age < 12) {
                            $context->buildViolation('Vous devez avoir au moins 12 ans')
                                    ->addViolation();
                        }
                    },
                ]),
            ],
        ])
        
        
        ->add('roles', ChoiceType::class, [
            'choices' => $roles,
            'placeholder' => 'Choose a role',
            'required' => true,
            'multiple' => false,
            'attr' => ['class' => 'form-control mb-3'],
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

        
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                      
                        'max' => 4096,
                    ]),
                ],
            ])
     


            ->add('poinds_sportif', null, [
               
                'required' => false,  
                'attr' => [
                    'class' => 'form-control mb-3 d-none', 
                    'placeholder' => 'Poids sportif'
                ],
                'constraints' => [
                 
                ],
            ])
            ->add('taille_sportif', null, [
                
                'required' => false, 
                'attr' => [
                    'class' => 'form-control mb-3 d-none', 
                    'placeholder' => 'Taille sportif'
                ],
                'constraints' => [
                    
                ],
            ]);
            
           
           
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
