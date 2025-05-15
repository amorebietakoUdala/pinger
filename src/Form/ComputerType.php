<?php

namespace App\Form;

use App\Entity\Default\Computer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComputerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $readonly = $options['readonly'];
        $builder
            ->add('hostname', null, [
                'disabled' => $readonly,
                'label' => 'computer.hostname',
            ])
            ->add('ip', null, [
                'disabled' => $readonly,
                'label' => 'computer.ip',
            ])
            ->add('mac', null, [
                'disabled' => $readonly,
                'label' => 'computer.mac',
            ])
            ->add('lastSucessfullPing',  DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-MM-dd',
                'disabled' => $readonly,
                'label' => 'computer.lastSucessfullPing',
            ])
            ->add('lastInventory',  DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-MM-dd',
                'disabled' => $readonly,
                'label' => 'computer.lastInventory',
            ])
            ->add('lastOcsContact',  DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-MM-dd',
                'disabled' => $readonly,
                'label' => 'computer.lastOcsContact',
            ])
            ->add('origin', null, [
                'disabled' => $readonly,
                'label' => 'computer.origin',
            ])
            ->add('necessary', CheckboxType::class, [
                'disabled' => $readonly,
                'label' => 'computer.necessary',
            ])
            ->add('notes', TextareaType::class, [
                'disabled' => $readonly,
                'label' => 'computer.notes',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Computer::class,
            'readonly' => false,
        ]);
    }
}
