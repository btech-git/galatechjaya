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
use AppBundle\Entity\Transaction\PurchaseInvoiceHeader;
use AppBundle\Entity\Transaction\PurchaseInvoiceDetail;
use AppBundle\Entity\Transaction\ReceiveHeader;

class PurchaseInvoiceHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', DateType::class)
            ->add('taxInvoiceCode')
            ->add('supplierInvoice')
            ->add('note')
            ->add('purchaseInvoiceDetails', CollectionType::class, array(
                'entry_type' => PurchaseInvoiceDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new PurchaseInvoiceDetail(),
                'label' => false,
            ))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $purchaseInvoiceHeader = $event->getData();
                $options['service']->initialize($purchaseInvoiceHeader, $options['init']);
                $form = $event->getForm();
                $formOptions = array(
                    'class' => ReceiveHeader::class,
                );
                if (!empty($purchaseInvoiceHeader->getId())) {
                    $formOptions['disabled'] = true;
                }
                $form->add('receiveHeader', EntityTextType::class, $formOptions);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $purchaseInvoiceHeader = $event->getData();
                $options['service']->finalize($purchaseInvoiceHeader);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PurchaseInvoiceHeader::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
