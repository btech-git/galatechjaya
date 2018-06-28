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
use AppBundle\Entity\Transaction\ReceiveHeader;
use AppBundle\Entity\Transaction\ReceiveDetail;
use AppBundle\Entity\Transaction\PurchaseOrderHeader;

class ReceiveHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', DateType::class)
            ->add('reference')
            ->add('note')
            ->add('warehouse')
            ->add('receiveDetails', CollectionType::class, array(
                'entry_type' => ReceiveDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new ReceiveDetail(),
                'label' => false,
            ))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $receiveHeader = $event->getData();
                $options['service']->initialize($receiveHeader, $options['init']);
                $form = $event->getForm();
                $formOptions = array(
                    'class' => PurchaseOrderHeader::class,
                );
                if (!empty($receiveHeader->getId())) {
                    $formOptions['disabled'] = true;
                }
                $form->add('purchaseOrderHeader', EntityTextType::class, $formOptions);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $receiveHeader = $event->getData();
                $options['service']->finalize($receiveHeader);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ReceiveHeader::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
