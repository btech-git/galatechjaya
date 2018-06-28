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
use AppBundle\Entity\Transaction\SaleReturnHeader;
use AppBundle\Entity\Transaction\SaleReturnDetail;
use AppBundle\Entity\Transaction\SaleInvoiceHeader;

class SaleReturnHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', DateType::class)
            ->add('note')
            ->add('shippingFee')
            ->add('saleReturnDetails', CollectionType::class, array(
                'entry_type' => SaleReturnDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new SaleReturnDetail(),
                'label' => false,
            ))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $saleReturnHeader = $event->getData();
                $options['service']->initialize($saleReturnHeader, $options['init']);
                $form = $event->getForm();
                $formOptions = array(
                    'class' => SaleInvoiceHeader::class,
                );
                if (!empty($saleReturnHeader->getId())) {
                    $formOptions['disabled'] = true;
                }
                $form->add('saleInvoiceHeader', EntityTextType::class, $formOptions);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $saleReturnHeader = $event->getData();
                $options['service']->finalize($saleReturnHeader);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SaleReturnHeader::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
