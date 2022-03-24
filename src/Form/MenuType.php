<?php

namespace App\Form;

use App\Entity\Carte;
use App\Entity\Menu;
use App\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('titre')
            ->add('descriptionCt')
            ->add('description')
            ->add('services', EntityType::class, [
                'class'=>Service::class,
                'choice_label'=>'name',
                'multiple'=>true,
                'expanded'=>false
            ])
            ->add('active')
            ->add('cartes', EntityType::class, [
                'class' => Carte::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false
            ])
            ->add('formules', CollectionType::class, array(
                'entry_type' => FormuleType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference'=> false
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }

    /* public function getChoices()
     {
         $choices=Menu::SERVICE;
         $output=[];
         foreach ($choices as $k => $v) {
            $output[$v]=$k;
         }
         return $output;
     }
     */
}
