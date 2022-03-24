<?php

namespace App\Form;

use App\Entity\Module\Contactation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject',HiddenType::class, array(
                'required' => true,
                'mapped'=>false))
            ->add('description',HiddenType::class, array(
                'required' => false,
                'mapped'=>false))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contactation::class,
        ]);
    }
}
