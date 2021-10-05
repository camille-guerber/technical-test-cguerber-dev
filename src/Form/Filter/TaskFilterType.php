<?php

namespace App\Form\Filter;

use App\Entity\Customer;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, [
                'label' => null,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Label'
                ]
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'required' => false,
                'placeholder' => 'User'
            ])
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'required' => false,
                'placeholder' => 'Customer',
            ])
            ->add('closed', ChoiceType::class, [
                'label' => 'Status',
                'placeholder' => 'Status',
                'required' => false,
                'choices' => [
                    'Opened' => false,
                    'Closed' => true,
                ],
            ])
            ->add('unassigned', HiddenType::class, [
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'get',
            'name' => null,
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
