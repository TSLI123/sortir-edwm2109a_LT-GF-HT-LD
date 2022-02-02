<?php

namespace App\Form;

use App\Entity\Ville;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;

class CreateVilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la ville : ',
                'required'=> true,
                'attr'=>[
                    'placeholder'=>'Nom de la ville'
                ]
            ])
            ->add('codePostal', TextType::class,[
                'label'=>'Code postal : ',
                'required' => true,
                'attr'=>[
                    'placeholder'=>'Code postal'
                ],
                'constraints'=> [
                    new Length(5),
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ville::class,
        ]);
    }
}
