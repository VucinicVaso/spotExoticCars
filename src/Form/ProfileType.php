<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProfileType extends AbstractType
{

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstname', TextType::class, ['label' => 'Firstname'])
                ->add('lastname', TextType::class, ['label' => 'Lastname']) 
                ->add('profile_image', FileType::class, array(
                    'label'      => 'Profile image',
                    'required'   => false,
                    'data_class' => null,
                    'attr'       => array(
                        'accept' => 'images/*'
                    )
                ))               
                ->add('city', TextType::class, ['label' => 'City'])                
                ->add('country', TextType::class, ['label' => 'Country'])
                ->add('email', EmailType::class, ['label' => 'Email'])
                ->add('dateofbirth', BirthdayType::class, [
                    'placeholder' => [
                        'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                    ]
                ])
                ->add('gender', ChoiceType::class, [
                    'choices'  => [
                        'male' => 'male',
                        'female' => 'female',
                    ],
                ])  
                ->add('update', SubmitType::class);        
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {  
        $resolver->setDefaults([
            'data_class' => \App\Entity\User::class
        ]);      
    }

}