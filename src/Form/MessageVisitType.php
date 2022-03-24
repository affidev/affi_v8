<?php

namespace App\Form;

use App\Entity\LogMessages\PrivateConvers;
use libphonenumber\PhoneNumberFormat;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class MessageVisitType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;

    /**
     * MessageVisitType constructor.
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){
            $user=$this->security->getUser();
            $form=$event->getForm();
            if(!$user){
                $form->add('username', TextType::class, array(
                    'label' => 'votre nom',
                    'mapped' => false,
                    'attr' => array(
                        'class' => 'validate[required, minSize[3], maxSize[150]] span12')
                ))

                ->add('email', EmailType::class, array(
                        'label' => 'votre email',
                        'required' => true,
                        'mapped' => false
                    ))

                ->add('contact', HiddenType::class, [
                        'mapped' => false
                    ])

                ->add('telephone', PhoneNumberType::class, array(
                    'default_region' => 'FR', 'format' => PhoneNumberFormat::NATIONAL,
                    'label' => 'telephone (mobile)',
                    'required' => false,
                    'mapped' => false
                ));
            }
        });


        $builder->add('energies', ChoiceType::class, array(
                'choices'  =>['bois'=>'bois', 'granulé'=>'granulé'],
                'multiple' => true,
                'expanded' => true,
                'mapped' => false
            ))

            ->add('subject', ChoiceType::class, array(
                'choices' => array(
                    "commerciale"=>"commercial",
                    "sav"=> "sav",
                    "autres"=>'autres',
                ),
                'multiple' => false,
                'expanded' => false,
                'mapped' => false
            ))

            ->add('content', TextareaType::class, array(
                'label' => 'votre message',
                'mapped' => false,
                'required' => true
            ))

            ->add('sectorcode', TextType::class, array(
                'mapped' => false,
                'attr'=>array('data_name'=>'code'),
                'label_format'=>'test_format',
                'required' => false,
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PrivateConvers::class,
        ]);
    }
}
