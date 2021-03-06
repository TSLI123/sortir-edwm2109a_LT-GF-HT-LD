<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo',TextType::class ,[
                'label' => 'Pseudo : ',
                'constraints' => new Length(2,2,30),
            ])
            ->add('prenom', TextType::class,[
                'label'=>'Prénom : ',
                'constraints' => new Length(2,2,30)
            ])
            ->add('nom', TextType::class,[
                'label'=> 'Nom : ',
                'constraints' => new Length(2,2,30)
            ])
            ->add('telephone', TelType::class,[
                'label' => 'Téléphone : ',
                'constraints' => new Length(10)
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email : '
            ])

            ->add('new_motPasse', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe et la confirmation doivent être identiques.',
                'label' => 'Nouveau mot de passe :',
                'required' =>false,
                'mapped'=>false,
                'constraints' =>
                new Length(8,8),
                'first_options' => [
                    'label' => 'Nouveau mot de passe : ',
                    'attr'=>[
                        'placeholder' => "Entrez votre nouveau mot de passe"
                    ]
                ],
                'second_options' => [
                    'label'=> 'Confirmez le mot de passe',
                    'attr'=>[
                        'placeholder'=> 'Merci de confirmer le mot de passe'
                    ]
                ]

            ])

            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                //'disabled' => true
            ] )
            ->add('imgProfil', FileType::class, [
                'data_class'=> null,
                'label' => false,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpg',
                            'image/png',
                            'image/jpeg'
                        ],
                        'mimeTypesMessage' => "Format invalide",
                    ])
                ],
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
            'data_class' => Participant::class,
        ]);
    }
}
