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
use AppBundle\Entity\Transaction\ExpenseHeader;
use AppBundle\Entity\Transaction\ExpenseDetail;
use AppBundle\Entity\Master\Account;

class ExpenseHeaderType extends AbstractType
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
            ->add('expenseDetails', CollectionType::class, array(
                'entry_type' => ExpenseDetailType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype_data' => new ExpenseDetail(),
                'label' => false,
            ))
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $expenseHeader = $event->getData();
                $options['service']->initialize($expenseHeader, $options['init']);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $expenseHeader = $event->getData();
                $options['service']->finalize($expenseHeader);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ExpenseHeader::class,
        ));
        $resolver->setRequired(array('service', 'init', 'accountRepository'));
    }
}
