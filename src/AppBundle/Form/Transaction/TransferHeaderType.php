<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use AppBundle\Entity\Transaction\TransferHeader;
use AppBundle\Entity\Transaction\TransferDetail;

class TransferHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', DateType::class)
            ->add('note')
            ->add('warehouseFrom')
            ->add('warehouseTo')
            ->add('transferDetails', CollectionType::class, array(
                'entry_type' => TransferDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new TransferDetail(),
                'label' => false,
            ))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $transferHeader = $event->getData();
                $options['service']->initialize($transferHeader, $options['init']);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $transferHeader = $event->getData();
                $options['service']->finalize($transferHeader);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TransferHeader::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
