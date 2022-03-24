<?php

namespace App\Form;

use App\Entity\Module\ModuleList;
use App\Entity\Websites\Website;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModuleListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $listewebsites=$options['listwebsite'];

        $builder
            ->add('website', EntityType::class, [
                'class' => Website::class,
                'choices' => $listewebsites,
                'choice_label'  => 'namewebsite',
                'mapped'=>false])
            ->add('classmodule')
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ModuleList::class,
            'listwebsite'=>null,

        ]);
    }
}
