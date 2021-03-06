<?php

namespace App\Form;

use App\Entity\Agenda\Subscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('starttime', DateType::class, [
        'input' => 'datetime',
        'widget' =>'choice',
        'label'=> 'date debut :',
        'mapped' => true
    ])
            ->add('endtime', DateType::class, [
        'input' => 'datetime',
        'widget' =>'choice',
        'label'=> 'date fin :',
        'mapped' => true
    ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Subscription::class,
        ]);
    }
}
