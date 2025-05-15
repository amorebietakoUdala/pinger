<?php

namespace App\Form;

use App\Entity\Default\Computer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComputerSearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('hostname', null , [
                'label' => 'computer.hostname',
                'required' => false,
            ])
            ->add('startIp', null , [
                'label' => 'computer.startip',
                'required' => false,
            ])
            ->add('endIp', null , [
                'label' => 'computer.endip',
                'required' => false,
            ])
            ->add('mac', null , [
                'label' => 'computer.mac',
                'required' => false,
            ])
            ->add('origin', ChoiceType::class , [
                'label' => 'computer.origin',
                'choices' => [
                    'Pinger' => 'Pinger',
                    'OCS' => 'OCS',
                    'Active Directory' => 'Active Directory',
                ],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
