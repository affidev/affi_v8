<?php

namespace App\Form;

use App\Entity\Food\ArticlesFormule;
use App\Entity\Food\CatFormule;
use App\Entity\Food\Formules;
use App\Entity\Food\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormulesOneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class, [
                'label' => 'Titre',
                'required' => false])
           /* ->add('theprice',NumberType::class, [
                'label' => 'prix menu',
                'required' => true,
                'input'=>'string',
                'mapped'=>false])
           */
           ->add('description',TextType::class, [
                'label' => 'description',
               'required' => false])
           ->add('start',DateType::class, [
               'label' => 'date début publication',
               'widget' => 'single_text',
               'required' => true,
               'mapped'=>false])
            ->add('end',DateType::class, [
                'label' => 'date fin publication',
                'widget' => 'single_text',
                'required' => true,
                'mapped'=>false])

            ->add('articles', EntityType::class,array(
                'class'=>ArticlesFormule::class,
                'choice_label'=>'titre',
                'multiple'=>true,
                'expanded'=>false
            ))
            ->add('catformules', EntityType::class,array(
                'class'=>CatFormule::class,
                'choice_label'=>'titre',
                'multiple'=>false,
                'expanded'=>true
            ))
            ->add('services', EntityType::class, [
                'class'=>Service::class,
                'choice_label'=>'name',
                'multiple'=>true,
                'expanded'=>false
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Formules::class,
        ]);
    }
}
