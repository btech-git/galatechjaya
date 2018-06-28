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
use AppBundle\Entity\Transaction\SalePaymentHeader;
use AppBundle\Entity\Transaction\SalePaymentDetail;
use AppBundle\Entity\Master\Customer;

class SalePaymentHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', DateType::class)
            ->add('note')
            ->add('customer', EntityTextType::class, array('class' => Customer::class))
            ->add('paymentType')
            ->add('salePaymentDetails', CollectionType::class, array(
                'entry_type' => SalePaymentDetailType::class,
                'entry_options' => array(
                    'accountRepository' => $options['accountRepository'],
                ),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new SalePaymentDetail(),
                'label' => false,
            ))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $salePaymentHeader = $event->getData();
                $options['service']->initialize($salePaymentHeader, $options['init']);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $salePaymentHeader = $event->getData();
                $options['service']->finalize($salePaymentHeader);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SalePaymentHeader::class,
        ));
        $resolver->setRequired(array('service', 'init', 'accountRepository'));
    }
}
