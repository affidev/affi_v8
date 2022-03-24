<?php

namespace App\Form;

use App\Entity\Food\MenuSearch;
use App\Entity\Food\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class MenuSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('maxPrix', IntegerType::class, [
                'required'=>false,
                'label'=>false,
                'attr'=>[
                    'placeholder'=>'prix maximum']
                ])
            ->add('minPrix', IntegerType::class, [
                'required'=>false,
                'label'=>false,
                'attr'=>[
                    'placeholder'=>'prix minimum']
                ])
             ->add('services', EntityType::class, [
            'class'=>Service::class,
            'required'=>false,
            'label'=>false,
            'choice_label'=>'name',
            'multiple'=>true
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MenuSearch::class,
            'method'=>'get',
            'csrf_protection'=>false
        ]);
    }

    public function getBlockPrefix()
    {
        return "";
    }
}
