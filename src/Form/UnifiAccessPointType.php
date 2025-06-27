<?php

namespace App\Form;


use App\Entity\Default\UnifiAccessPoint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UnifiAccessPointType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $readonly = $options['readonly'];
        $builder
            ->add('name', null, [
                'disabled' => $readonly,
                'label' => 'unifiAp.name',
            ])
            ->add('ip', null, [
                'disabled' => $readonly,
                'label' => 'unifiAp.ip',
            ])
            ->add('state', null, [
                'disabled' => $readonly,
                'label' => 'unifiAp.state',
            ])
            ->add('mac', null, [
                'disabled' => $readonly,
                'label' => 'unifiAp.mac',
            ])
            ->add('pingStatus',  null, [
                'disabled' => $readonly,
                'label' => 'unifiAp.pingStatus',
            ])
            ->add('lastSuccessfullPing',  DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-MM-dd',
                'disabled' => $readonly,
                'label' => 'unifiAp.lastSuccessfullPing',
            ])
            ->add('excludeFromReport',  CheckboxType::class, [
                'disabled' => $readonly,
                'label' => 'unifiAp.excludeFromReport',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UnifiAccessPoint::class,
            'readonly' => false,
        ]);
    }
}
