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
use AppBundle\Entity\Transaction\PurchaseReceiptHeader;
use AppBundle\Entity\Transaction\PurchaseReceiptDetail;
use AppBundle\Entity\Master\Supplier;

class PurchaseReceiptHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', DateType::class)
            ->add('note')
            ->add('supplier', EntityTextType::class, array('class' => Supplier::class))
            ->add('purchaseReceiptDetails', CollectionType::class, array(
                'entry_type' => PurchaseReceiptDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new PurchaseReceiptDetail(),
                'label' => false,
            ))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $purchaseReceiptHeader = $event->getData();
                $options['service']->initialize($purchaseReceiptHeader, $options['init']);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $purchaseReceiptHeader = $event->getData();
                $options['service']->finalize($purchaseReceiptHeader);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PurchaseReceiptHeader::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
