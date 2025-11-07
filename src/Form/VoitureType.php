<?php

namespace App\Form;

use App\Entity\Marque;
use App\Entity\Utilisateur;
use App\Entity\Voiture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VoitureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('modele')
            ->add('immatriculation')
            ->add('energie', ChoiceType::class, [
                'choices' => [
                    'Electrique' => 'Electrique',
                    'Thermique' => 'Thermique',
                    'Hybride' => 'Hybride',
                    'Gaz' => 'Gaz',
                ],
                'expanded' => false, // menu déroulant
                'multiple' => false,
            ])
            ->add('couleur')
            ->add('date_premiere_immatriculation')
            ->add('Utilisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'id',
            ])
            ->add('Marque', EntityType::class, [
                'class' => Marque::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voiture::class,
        ]);
    }
}
