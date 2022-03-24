<?php

namespace App\Form;

use App\Entity\Module\Formules;
use App\Entity\Food\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormulesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class, [
                'label' => 'Intitulé de la formule',
                'required' => false])
           /* ->add('theprice',NumberType::class, [
                'label' => 'prix menu',
                'required' => true,
                'input'=>'string',
                'mapped'=>false])

           ->add('description',TextType::class, [
                'label' => 'description',
               'required' => false])
           */
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

            ->add('catformules', CollectionType::class, array(
                'entry_type' => FormuleMenuType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference'=> false,
                'row_attr'=>['class'=>'tcat_posedit']

            ))
             ->add('services', EntityType::class, [
                'class'=>Service::class,
                'choice_label'=>'name',
                'multiple'=>false,
                'expanded'=>true,
                 'row_attr'=>['class'=>'list-service']
             ])

            ->add('listarticle', HiddenType::class,[
                'mapped'=>false
            ])

            ->add('save', SubmitType::class,[
                'attr'=>['class'=>'btn-send'],
                'label'=>'enregistrez'
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
