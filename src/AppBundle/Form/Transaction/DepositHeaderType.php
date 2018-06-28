<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\Common\Collections\Criteria;
use AppBundle\Entity\Transaction\DepositHeader;
use AppBundle\Entity\Transaction\DepositDetail;
use AppBundle\Entity\Master\Account;

class DepositHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', DateType::class)
            ->add('note')
            ->add('account', EntityType::class, array(
                'class' => Account::class,
                'choices' => $options['accountRepository']->findBy(array('isCashOrBank' => true), array('code' => Criteria::ASC)),
            ))
            ->add('depositDetails', CollectionType::class, array(
                'entry_type' => DepositDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new DepositDetail(),
                'label' => false,
            ))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $depositHeader = $event->getData();
                $options['service']->initialize($depositHeader, $options['init']);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $depositHeader = $event->getData();
                $options['service']->finalize($depositHeader);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => DepositHeader::class,
        ));
        $resolver->setRequired(array('service', 'init', 'accountRepository'));
    }
}
