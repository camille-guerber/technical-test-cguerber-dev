<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'Task label',
                'required' => false,
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'required' => false,
                'placeholder' => '',
            ])
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'required' => false,
                'placeholder' => '',
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'mapped' => false,
                'placeholder' => '',
                'required' => false,
                'choices' => [
                    'Opened' => false,
                    'Closed' => true,
                ],
            ])
            ->add('submit', SubmitType::class)
            ->setMethod('GET')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
