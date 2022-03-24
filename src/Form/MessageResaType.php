<?php

namespace App\Form;

use App\Entity\LogMessages\CommentNotice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class MessageResaType extends AbstractType
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
                $form
                    ->add('sexe', ChoiceType::class, [
                        'choices'=>array(
                            'Madame'=>1,
                            'Monsieur'=>2),
                        'label' => 'Mme, M...',
                        'mapped' => false
                    ])
                    ->add('firstname', TextType::class, array(
                        'label' => 'Nom',
                        'attr' => array(
                            'class' => 'validate[required, minSize[3], maxSize[150]] span12'
                        ),
                        'mapped' => false
                    ))
                    ->add('lastname', TextType::class, array(
                        'label' => 'prenom',
                        'attr' => array(
                            'class' => 'validate[required, minSize[3], maxSize[150]] span12'
                        ),
                        'mapped' => false
                    ))

                    ->add('username', TextType::class, array(
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

                    ->add('telephone', TextType::class, array(
                        'label' => 'telephone',
                        'required' => false,
                        'mapped' => false
                    ));
            }
        });


        $builder
            ->add('datereserve', HiddenType::class, [
                'mapped' => false
                ])

            ->add('strtimereserve', HiddenType::class, [
                'mapped' => false
            ])

            ->add('nbCouverts', IntegerType::class, [
                'required' =>true,
                'mapped' => false,
                'attr'=>[
                    'value'=>1,
                    'min'=>1,
                    'max'=>10]
            ])
            ->add('salle', ChoiceType::class, [
                'choices'=>array(
                    'Intérieur'=>1,
                    'Terrasse'=>2),
                'mapped' => false
            ])

            ->add('content', TextareaType::class, array(
                'label' => 'votre message',
                'mapped' => false,
                'required' => false
            ))

            ->add('contact', HiddenType::class, [
                'mapped' => false
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CommentNotice::class,
        ]);
    }
}
