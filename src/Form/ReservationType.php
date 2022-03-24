<?php

namespace App\Form;

use App\Entity\Marketplace\Resaresto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;



class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateStartPeriod', DateType::class, [
                'input' => 'datetime',
                'widget' =>'choice',
                'mapped' => false
            ])
            ->add('nbCouverts', IntegerType::class, [
                'required' =>true,
                'attr'=>[
                    'value'=>1,
                    'min'=>1,
                    'max'=>10],
                'mapped' => false
            ])
            ->add('salle', ChoiceType::class, [
                'choices'=>array(
                    'Intérieur'=>1,
                    'Terrasse'=>2),
                'mapped' => false
            ])
            ->add('heuredebut', TimeType::class, array(
                'label' => 'heure',
                'input' => 'datetime',
                'widget' =>'choice',
                'mapped' => false))
            ->add('sexe', ChoiceType::class, [
                'choices'=>array(
                    'Madame'=>1,
                    'Monsieur'=>2),
                'label' => 'Mme, M...',
                'mapped' => false
            ])
            ->add('firstname', TextType::class, array(
                'label' => 'Nom',
                'attr' => array(
                    'class' => 'validate[required, minSize[3], maxSize[150]] span12'
                ),
                'mapped' => false
            ))
            ->add('lastname', TextType::class, array(
                'label' => 'prenom',
                'attr' => array(
                    'class' => 'validate[required, minSize[3], maxSize[150]] span12'
                ),
                'mapped' => false
            ))
            ->add('emailResa')
            ->add('telephone', TextType::class, array(
                'label' => 'telephone (fixe)',
                'required'=>false,
            ))
            ->add('reserver', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Resaresto::class,
        ]);
    }
}
