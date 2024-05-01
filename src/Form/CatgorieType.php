<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityManagerInterface;


class CategorieType extends AbstractType
{
  
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_categorie', TextType::class, [
                'label' => 'Nom de la catégorie',
                'attr' => ['class' => 'form-control'],
            
                
            ])
            ->add('description_categorie', TextareaType::class, [
                'label' => 'Description de la catégorie',
                'attr' => ['class' => 'form-control'],
            
                
            ])
            ->add('type_categorie', TextareaType::class, [
                'label' => 'Type de la catégorie',
                'attr' => ['class' => 'form-control'],
            
                
            ])
            ->add('Ajouter', SubmitType::class);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}
