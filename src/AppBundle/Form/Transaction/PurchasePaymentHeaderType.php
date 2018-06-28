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
use AppBundle\Entity\Transaction\PurchasePaymentHeader;
use AppBundle\Entity\Transaction\PurchasePaymentDetail;
use AppBundle\Entity\Master\Supplier;

class PurchasePaymentHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', DateType::class)
            ->add('note')
            ->add('supplier', EntityTextType::class, array('class' => Supplier::class))
            ->add('paymentType')
            ->add('purchasePaymentDetails', CollectionType::class, array(
                'entry_type' => PurchasePaymentDetailType::class,
                'entry_options' => array(
                    'accountRepository' => $options['accountRepository'],
                ),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new PurchasePaymentDetail(),
                'label' => false,
            ))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $purchasePaymentHeader = $event->getData();
                $options['service']->initialize($purchasePaymentHeader, $options['init']);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $purchasePaymentHeader = $event->getData();
                $options['service']->finalize($purchasePaymentHeader);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PurchasePaymentHeader::class,
        ));
        $resolver->setRequired(array('service', 'init', 'accountRepository'));
    }
}
