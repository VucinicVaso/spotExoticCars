<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class NewsType extends AbstractType
{

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, ['label' => 'Title'])
                ->add('body', TextType::class, ['label' => 'Body']) 
                ->add('images', FileType::class, array(
                    'label'    => 'Images [max:6]',
                    'required' => false,
                    'multiple' => true,
                    'attr'     => array(
                        'accept' => 'images/*',
                        'multiple' => 'multiple'
                    )
                ))
                ->add('create', SubmitType::class);        
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {  
        $resolver->setDefaults([
            'data_class' => \App\Entity\News::class
        ]);      
    }

}