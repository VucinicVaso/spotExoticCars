<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ContactType extends AbstractType
{

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, ['label' => 'Name'])
                ->add('email', EmailType::class, ['label' => 'Email'])
                ->add('subject', TextType::class, ['label' => 'Subject']) 
                ->add('message', TextType::class, ['label' => 'Your message']) 
                ->add('Create', SubmitType::class);        
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {  
        $resolver->setDefaults([
            'data_class' => \App\Entity\Contact::class
        ]);      
    }

}