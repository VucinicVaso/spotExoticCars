<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RegisterType extends AbstractType
{

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstname', TextType::class, ['label' => 'Firstname'])
                ->add('lastname', TextType::class, ['label' => 'Lastname'])             
                ->add('city', TextType::class, ['label' => 'City'])                
                ->add('country', TextType::class, ['label' => 'Country'])                                
                ->add('email', EmailType::class, ['label' => 'Email'])
                ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => ['label' => 'Password'],
                    'second_options' => ['label' => 'Repeated Password']
                ])
                ->add('date_of_birth', BirthdayType::class, [
                    'placeholder' => [
                        'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                    ]
                ])
                ->add('gender', ChoiceType::class, [
                    'choices'  => [
                        '' => '',
                        'male' => 'male',
                        'female' => 'female',
                    ],
                ])
                ->add('termsAgreed', CheckboxType::class, [
                    'mapped' => false,
                    'constraints' => new IsTrue(),
                    'label' => 'I agree to the terms of service'
                ])
                ->add('register', SubmitType::class);        
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {  
        $resolver->setDefaults([
            'data_class' => \App\Entity\User::class
        ]);      
    }	

}