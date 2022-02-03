<?php

namespace App\Form;


use App\Entity\Lieu;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom : '])
            ->add('rue', TextType::class, [
                'label'  => 'Rue : '
            ])
            ->add('latitude', TextType::class, [
                'label' => 'Latitude : '
            ])
            ->add('longitude', TextType::class, [
                'label' => 'Longitude : '
            ])
            ->add('ville', TextType::class, [
                'label'=>'Ville : ',
            ])
            ->add('submit', SubmitType::class,[
                'label'=>'Enregistrer'
            ])
            ->add('reset', ResetType::class,[
                'label'=>'Annuler'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
