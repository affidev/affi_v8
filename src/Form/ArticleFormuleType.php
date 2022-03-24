<?php

namespace App\Form;

use App\Entity\Food\ArticlesFormule;
use App\Entity\Food\Categories;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ArticleFormuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('titre',TextType::class, array(
        'label' => 'nom du plat',
        'attr' => array(
            'class' => 'validate[minSize[3], maxSize[150]] span12'
        ),
        'required'=>true,
        ))
        ->add('prix')

        ->add('categorie', EntityType::class, [
            'class'=>Categories::class,
            'choice_label'=>'name',
            'multiple'=>false,
            'expanded'=>false
        ])
        ->add('composition',TextareaType::class, array(
            'label' => 'Composition :',
            'attr' => array(
                'class' => 'validate[minSize[0], maxSize[250]] span12'
            ),
            'required'=>false,
        ))
        ->add('descriptif',TextareaType::class, array(
            'label' => 'Description :',
            'attr' => array(
                'class' => 'validate[minSize[0], maxSize[250]] span12'
            ),
            'required'=>false,
        ))
            ->add('infos',TextareaType::class, array(
                'label' => 'Commentaire :',
                'attr' => array(
                    'class' => 'validate[minSize[0], maxSize[250]] span12'
                ),
                'required'=>false,
            ))
        ->add('pict',PictType::class, [
            'label' => 'image',
            'required'=>false,
            'mapped'=>false])

        ->add('base64',HiddenType::class, [
            'label' => 'image 64',
            'required'=>false,
            'mapped'=>false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ArticlesFormule::class,
        ]);
    }
}
