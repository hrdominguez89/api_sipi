<?php

namespace App\Form;

use App\Entity\Programs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class ProgramsType extends AbstractType
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
            ->add('version', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotNull(),
                    new NotBlank()
                ]
            ])
            ->add('observations', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Programs::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ]);
    }
}
