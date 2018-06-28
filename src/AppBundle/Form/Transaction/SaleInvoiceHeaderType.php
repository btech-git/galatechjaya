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
use AppBundle\Entity\Transaction\SaleInvoiceHeader;
use AppBundle\Entity\Transaction\SaleInvoiceDetail;
use AppBundle\Entity\Master\Customer;

class SaleInvoiceHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', DateType::class)
            ->add('customerOrderNumber')
            ->add('taxInvoiceCode')
            ->add('discountPercentage')
            ->add('shippingFee')
            ->add('note')
            ->add('isTax')
            ->add('customer', EntityTextType::class, array('class' => Customer::class))
            ->add('saleInvoiceDetails', CollectionType::class, array(
                'entry_type' => SaleInvoiceDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new SaleInvoiceDetail(),
                'label' => false,
            ))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $saleInvoiceHeader = $event->getData();
                $options['service']->initialize($saleInvoiceHeader, $options['init']);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $saleInvoiceHeader = $event->getData();
                $options['service']->finalize($saleInvoiceHeader);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SaleInvoiceHeader::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
