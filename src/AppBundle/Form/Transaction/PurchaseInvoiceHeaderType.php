<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Transaction\PurchaseInvoiceHeader;

class PurchaseInvoiceHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate')
            ->add('taxInvoiceCode')
            ->add('supplierInvoice')
            ->add('subTotal')
            ->add('discount')
            ->add('taxNominal')
            ->add('shippingFee')
            ->add('totalReturn')
            ->add('grandTotal')
            ->add('note')
            ->add('isTax')
            ->add('staffFirst')
            ->add('staffLast')
            ->add('receiveHeader')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PurchaseInvoiceHeader::class,
        ));
    }
}
