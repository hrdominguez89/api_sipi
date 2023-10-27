<?php

namespace App\Form;

use App\Entity\Computers;
use App\Entity\StatusComputer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class ComputersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotNull(),
                    new NotBlank()
                ]
            ])
            ->add('brand', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotNull(),
                    new NotBlank()
                ]
            ])
            ->add('model', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotNull(),
                    new NotBlank()
                ]
            ])
            ->add('serie', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotNull(),
                    new NotBlank()
                ]
            ])
            ->add('statusComputer', EntityType::class, [
                'class' => StatusComputer::class,
                'constraints' => [
                    new NotNull(),
                    new NotBlank(),
                ]
            ])
            ->add('details', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Computers::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ]);
    }
}
