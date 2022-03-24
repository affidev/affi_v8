<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MarketationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre',TextType::class, array(
                'label' => 'Titre',
                'required' => true,
                'mapped'=>false))
            ->add('soustitre',TextType::class, array(
                'label' => 'base line',
                'required' => false,
                'mapped'=>false))
            ->add('description',TextType::class, array(
                'label' => 'mots clés',
                'required' => true,
                'mapped'=>false))
            ->add('contenuone',TextType::class, array(
                'label' => 'Description',
                'required' => false,
                'mapped'=>false))
            ->add('contenutwo',TextType::class, array(
                'label' => 'autre description',
                'required' => false,
                'mapped'=>false))
            ->add('sector',TextType::class, array(
                'label' => 'Lieu',
                'required' => false,
                'attr' => array(
                    'class' => 'validate[required] span12',
                    'data-google' => 'text',
                    'infos' => "localisation du marché."
                )))
            ->add('image', FileType::class, array(
                'label' => 'image',
                'required' => false,
                'mapped'=>false))
            ->add('save', SubmitType::class)
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Eventation::class,
        ]);
    }
}
