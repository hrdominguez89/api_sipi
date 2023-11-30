<?php

namespace App\Form;

use App\Entity\Requests;
use App\Entity\StatusRequest;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class RequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('requestedDate', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'constraints' => [
                    new NotNull(),
                    new NotBlank(),
                ]
            ])
            ->add('requestedAmount', NumberType::class, [
                'required' => true,
                'constraints' => [
                    new NotNull(),
                    new NotBlank(),
                ]
            ])
            ->add('requestedPrograms', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotNull(),
                    new NotBlank()
                ]
            ])
            ->add('observations', TextType::class, [
                'required' => false
            ])
            // ->add('statusRequest', EntityType::class, [
            //     'class' => StatusRequest::class,
            //     'required' => true,
            //     'constraints' => [
            //         new NotNull(),
            //         new NotBlank(),
            //     ]
            // ])
            ->add('professor', EntityType::class, [
                'class' => User::class,
                'constraints' => [
                    new NotNull(),
                    new NotBlank(),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Requests::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ]);
    }
}
