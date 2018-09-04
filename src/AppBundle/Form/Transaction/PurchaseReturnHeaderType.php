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
use AppBundle\Entity\Transaction\PurchaseReturnHeader;
use AppBundle\Entity\Transaction\PurchaseReturnDetail;
use AppBundle\Entity\Transaction\PurchaseInvoiceHeader;

class PurchaseReturnHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', DateType::class)
            ->add('note')
            ->add('shippingFee')
            ->add('warehouse')
            ->add('purchaseReturnDetails', CollectionType::class, array(
                'entry_type' => PurchaseReturnDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new PurchaseReturnDetail(),
                'label' => false,
            ))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $purchaseReturnHeader = $event->getData();
                $options['service']->initialize($purchaseReturnHeader, $options['init']);
                $form = $event->getForm();
                $formOptions = array(
                    'class' => PurchaseInvoiceHeader::class,
                );
                if (!empty($purchaseReturnHeader->getId())) {
                    $formOptions['disabled'] = true;
                }
                $form->add('purchaseInvoiceHeader', EntityTextType::class, $formOptions);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $purchaseReturnHeader = $event->getData();
                $options['service']->finalize($purchaseReturnHeader);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PurchaseReturnHeader::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
