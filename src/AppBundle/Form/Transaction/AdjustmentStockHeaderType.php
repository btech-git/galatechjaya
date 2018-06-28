<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use LibBundle\Form\Type\EntityTextType;
use AppBundle\Entity\Transaction\AdjustmentStockHeader;
use AppBundle\Entity\Transaction\AdjustmentStockDetail;
use AppBundle\Entity\Master\Warehouse;

class AdjustmentStockHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', DateType::class)
            ->add('note')
            ->add('warehouse')
            ->add('adjustmentStockDetails', CollectionType::class, array(
                'entry_type' => AdjustmentStockDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new AdjustmentStockDetail(),
                'label' => false,
            ))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $adjustmentStockHeader = $event->getData();
                $options['service']->initialize($adjustmentStockHeader, $options['init']);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $adjustmentStockHeader = $event->getData();
                $options['service']->finalize($adjustmentStockHeader);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => AdjustmentStockHeader::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
