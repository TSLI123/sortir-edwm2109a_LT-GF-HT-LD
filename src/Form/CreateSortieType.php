<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;

use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie : '
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'html5' => true,
                'widget' => 'single_text',
                'label' => 'Date et heure de la sortie : '
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'html5' => true,
                'widget' => 'single_text',
                'label' => "Date limite d'inscription : ",
            ])
            ->add('nbInscriptionsMax', NumberType::class, [
                'label' => 'Nombre de places : ',
            ])
            ->add('duree', NumberType::class, [
                'html5' => true,
                'label' => 'Durée (minute): ',
                'scale' => 0,

                'attr' => array(
                    'min' => 60,
                    'max' => 240,
                    'step' => 10,
                    'placeholder' => '90',
                )
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Description et infos : ',

            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'label' => 'Campus : ',
                'choice_label' => 'nom',
                'attr' => ['readonly' => true]
            ])
            ->add('ville', EntityType::class, [
                'mapped' => false,
                'class' => Ville::class,
                'choice_label' => 'nom',
                'placeholder' => 'saisir une ville'
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                //'choices'=>[]
            ])

            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
            ])
            ->add('publier', SubmitType::class, [
                'label' => 'Publier',]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
