<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Transaction\PurchasePaymentHeader;

class PurchasePaymentHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate')
            ->add('note')
            ->add('totalAmount')
            ->add('supplier')
            ->add('paymentType')
            ->add('staffFirst')
            ->add('staffLast')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PurchasePaymentHeader::class,
        ));
    }
}
