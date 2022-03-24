<?php

namespace App\Form;

use App\Entity\Module\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isUser')
            ->add('user')
            ->add('contact')
            ->add('nbCouverts')
            ->add('commentaire')
            ->add('salle')
            ->add('dateresa_at')
            ->add('tag')
            ->add('datecre_at')
            ->add('datemodif_at')
            ->add('deleted')
            ->add('resaconfirmed')
            ->add('msgsend')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
