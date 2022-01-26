<?php

namespace App\Form;

use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;

class ListSortieType extends AbstractType
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
                'required' => false
            ])

            ->add('entre', DateType::class, [
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('et', DateType::class, [
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('choix', ChoiceType::class, [
                'choices' => [
                    "Sorties dont je suis l'organisateur/trice" => 'org',
                    'Sorties auquelles je suis inscrit/e' => 'ins',
                    'Sorties auquelles je ne suis pas inscrit/e' => 'pasIns',
                    'Sorties passées' => 'passe'
                ],
                'multiple' => true,
                'expanded' => true
            ])
        ;
    }

}
