<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use App\Entity\Categorie;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;


class ProduitType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $categoryChoices = $options['categoryChoices']; 

        $builder
            ->add('nom_prod')
            ->add('prix_prod')
            ->add('description_prod', TextareaType::class)
            ->add('quantite_prod')
            ->add('image_prod', FileType::class, [
                'label' => 'Image du produit',
                'required' => false,
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom_categorie',
                'placeholder' => 'Choose a category',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('rating')

            ->add('Ajouter', SubmitType::class);
            
        $builder->get('image_prod')->addModelTransformer(new class() implements DataTransformerInterface {
            public function transform($value)
            {
                if ($value instanceof \Symfony\Component\HttpFoundation\File\File) {
                    return $value->getFilename();
                }
    
                return null;
            }
    
            public function reverseTransform($value)
            {
                if ($value instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    return $value;
                }
    
                if (is_string($value)) {
                    $uploadDirectory = $this->container->getParameter('kernel.project_dir') . '/public/assets/';
                    $filePath = $uploadDirectory . $value;
    
                    if (file_exists($filePath)) {
                        return new \Symfony\Component\HttpFoundation\File\File($filePath);
                    }
    
                    throw new TransformationFailedException("File not found: $value");
                }
    
                return null;
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        
        $resolver->setRequired('categoryChoices');
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
