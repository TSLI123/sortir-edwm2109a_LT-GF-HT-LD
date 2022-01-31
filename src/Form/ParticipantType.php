<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ParticipantType extends AbstractType
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
            ->add('administrateur', CheckboxType::class, [
                'required' => false,
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
            ] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
