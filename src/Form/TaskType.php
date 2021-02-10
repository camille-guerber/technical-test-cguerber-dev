<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'required' => false,
                'placeholder' => 'Not assigned'
            ])
            ->add('customer', EntityType::class, [
                'class' => Customer::class
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
