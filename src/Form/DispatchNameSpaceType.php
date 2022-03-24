<?php

namespace App\Form;

use App\Entity\DispatchSpace\DispatchSpaceWeb;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DispatchNameSpaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'modifier le nom de votre espace',
                'attr' => array(
                    'class' => 'validate[required, minSize[3], maxSize[70]] span12'
                )
            ))

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DispatchSpaceWeb::class,
        ]);
    }
}
