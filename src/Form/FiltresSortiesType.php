<?php

namespace App\Form;

use App\Classes\FiltresSorties;
use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class FiltresSortiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'multiple' => false
            ])
            ->add('search', SearchType::class, [
                'required' => false,
                'constraints' => [
                    new Length(['max' => 50]),
                ],
            ])
            ->add('from', DateType::class, [
                'label' => 'entre',
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('to', DateType::class, [
                'label' => 'et',
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('choice', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    "Sorties dont je suis l'organisateur/trice" => 'organisateur',
                    'Sorties auquelles je suis inscrit/e' => 'inscrit',
                    'Sorties auquelles je ne suis pas inscrit/e' => '/inscrit',
                    'Sorties passées' => 'past'
                ],
                'multiple' => true,
                'expanded' => true,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FiltresSorties::class,
        ]);
    }
}
