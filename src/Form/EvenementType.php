<?php

namespace App\Form;

use App\Entity\Evenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Component\Validator\Constraints\NotNull;



class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           // ->add('idEvent')
            ->add('NomEvent')
            ->add('Type')
            ->add('organisateur')
            ->add('Date_deb', null, [
                'widget' => 'single_text'
            ])
            
            ->add('Date_fin', null, [
                'widget' => 'single_text'
            ])
            ->add('Capacite')
            ->add('Image', FileType::class, array('data_class' => null))
           
           // ->add('nb_place_res')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
