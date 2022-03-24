<?php


namespace App\Form;


use App\Entity\Module\Market;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


class MarketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('adress', null, array(
                'label' => 'Lieu',
                'required' => true,
                'attr' => array(
                    'class' => 'validate[required] span12',
                    'data-google' => 'text',
                    'infos' => "localisation du marché."
                )
            ))
            ->add('street_number', HiddenType::class, array(
                'mapped'=> true
            ))

            ->add('route', HiddenType::class, array(
                'mapped'=> true
            ))

            ->add('country', HiddenType::class, array(
                'mapped'=> true
            ))

            ->add('latitude', HiddenType::class, array(
                'mapped'=> false
            ))

            ->add('longitude', HiddenType::class, array(
                'mapped'=> false
            ))

            ->add('locality', HiddenType::class, array(
                'mapped'=> true
            ))

            ->add('postal_code', HiddenType::class, array(
                'mapped'=> true
            ))

            ->add('save', SubmitType::class);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Market::class,
        ]);
    }
}
