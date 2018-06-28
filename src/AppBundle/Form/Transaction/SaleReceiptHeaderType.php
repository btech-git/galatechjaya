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
use AppBundle\Entity\Transaction\SaleReceiptHeader;
use AppBundle\Entity\Transaction\SaleReceiptDetail;
use AppBundle\Entity\Master\Customer;

class SaleReceiptHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', DateType::class)
            ->add('note')
            ->add('customer', EntityTextType::class, array('class' => Customer::class))
            ->add('saleReceiptDetails', CollectionType::class, array(
                'entry_type' => SaleReceiptDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new SaleReceiptDetail(),
                'label' => false,
            ))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $saleReceiptHeader = $event->getData();
                $options['service']->initialize($saleReceiptHeader, $options['init']);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $saleReceiptHeader = $event->getData();
                $options['service']->finalize($saleReceiptHeader);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SaleReceiptHeader::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
