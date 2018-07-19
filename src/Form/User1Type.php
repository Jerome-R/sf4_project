<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Security\Core\Security;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

use Symfony\Component\Form\CallbackTransformer;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class User1Type extends AbstractType
{
    private $security;

    public function __construct(Security $security){
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class)
            ->add('username', TextType::class)
            ->add('email', EmailType::class)
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
                'invalid_message' => 'The password fields must match.',
                'required' => false
                //'error_bubbling' => true,
            ])
            //->add('token')
            ->add('isActive', CheckboxType::class, ['required' => false])
            //->add('lastLogin')
            //->add('createdAt')
            //->add('updatedAt')
            ->add('credentialExpireAt', TextType::class, ['required' => false])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            $user =$this->security->getUser();

            if( in_array("ROLE_SUPER_ADMIN", $user->getRoles()) ) {
                $choices = array( 
                    'ROLE_BOUTIQUE'         => 'ROLE_BOUTIQUE',
                    'ROLE_DIRECTEUR'        => 'ROLE_DIRECTEUR',
                    'ROLE_RETAIL_MANAGER'   => 'ROLE_RETAIL_MANAGER',
                    'ROLE_SIEGE'            => 'ROLE_SIEGE',
                    'ROLE_ADMIN'            => 'ROLE_ADMIN'
                );
            }
            elseif( in_array("ROLE_ADMIN", $user->getRoles()) ) {
                $choices = array( 
                    'ROLE_BOUTIQUE'         => 'ROLE_BOUTIQUE',
                    'ROLE_DIRECTEUR'        => 'ROLE_DIRECTEUR',
                    'ROLE_RETAIL_MANAGER'   => 'ROLE_RETAIL_MANAGER',
                    'ROLE_SIEGE'            => 'ROLE_SIEGE',
                    'ROLE_ADMIN'            => 'ROLE_ADMIN'
                );
            }

            $form->add('roles', choiceType::class, array(
                'choices'  => $choices,
                'multiple' => true
                )
            );
        });



        /*$builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesAsArray) {
                    // transform the array to a string
                    return implode(', ', $rolesAsArray);
                },
                function ($rolesAsString) {
                    // transform the string back to an array
                    return explode(', ', $rolesAsString);
                }
            ))
        ;*/
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
