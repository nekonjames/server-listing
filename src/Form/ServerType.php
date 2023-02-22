<?php

namespace App\Form;

use App\Dto\ServerDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $ram = [
            '2G' => '2',
            '4GB' => '4',
            '8GB' => '8',
            '12GB' => '12',
            '16GB' => '16',
            '24GB' => '24',
            '32GB' => '32',
            '48GB' => '48',
            '64GB' => '64',
            '96GB' => '96',
        ];
        $builder
            ->add('storage', RangeType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 100000,
                    'class' => 'tinymce'
                ],
            ])
            ->add('ram', ChoiceType::class, [
                'choices' => $ram,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('diskType', ChoiceType::class, [
                'choices' => [
                    'all' => 'all',
                    'SATA' => 'SATA',
                    'SSD' => 'SSD',
                    'SAS' => 'SAS',
                ],
            ])
            ->add('location', ChoiceType::class, [
                'choices' => [
                    'all' => 'all',
                    'Amsterdam' => 'Amsterdam',
                    'Dallas' => 'Dallas',
                    'Frankfurt' => 'Frankfurt',
                    'San Francisco' => 'San Francisco',
                    'Washington D.C' => 'Washington D.C',
                ],
            ])
            ->add('filter', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ServerDto::class,
        ]);
    }
}
