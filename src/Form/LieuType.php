<?php

namespace App\Form;


use App\Entity\Lieu;

use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom : ',
                'constraints' => new Length(2,1,120),
                'required' => true])
            ->add('rue', TextType::class, [
                'label'  => 'Rue : ',
                'constraints' => new Length(2,1,120),
                'required' => true
            ])
            ->add('latitude', TextType::class, [
                'label' => 'Latitude : '
            ])
            ->add('longitude', TextType::class, [
                'label' => 'Longitude : '
            ])
            ->add('ville', EntityType::class, [
                'mapped' => false,
                'class' => Ville::class,
                'choice_label' => 'nom',
                'placeholder' => 'saisir une ville'])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
                'placeholder' => 'saisir une ville'
            ])
            ->add('submit', SubmitType::class,[
                'label'=>'Enregistrer'
            ])
            ->add('reset', ResetType::class,[
                'label'=>'Annuler'
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
