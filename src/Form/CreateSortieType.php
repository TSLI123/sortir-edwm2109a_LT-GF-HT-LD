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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
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
                'label' => 'Date et heure de la sortie : ',
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
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
            ])
            ->add('publier', SubmitType::class, [
                'label' => 'Publier',
            ])
            ->add('reset', ResetType::class, [
                'label' => 'Annuler'
            ]);

        $formModifier = function (FormInterface $form, Ville $ville = null) {
            $lieux = null === $ville ? [] : $ville->getLieux();

            $form->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'placeholder' => '',
                'choice_label' => 'nom',
                'choices' => $lieux,
            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $formModifier($event->getForm(), null);
            }
        );

        $builder->get('ville')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $ville = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $ville);
            }
        );


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
