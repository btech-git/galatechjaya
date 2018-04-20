<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Transaction\SaleCheque;

class SaleChequeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate')
            ->add('dateDue')
            ->add('chequeNumber')
            ->add('amount')
            ->add('bankName')
            ->add('note')
            ->add('staffFirst')
            ->add('staffLast')
            ->add('saleReceiptHeader')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SaleCheque::class,
        ));
    }
}
