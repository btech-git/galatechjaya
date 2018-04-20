<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Transaction\SaleInvoiceHeader;

class SaleInvoiceHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate')
            ->add('taxInvoiceCode')
            ->add('customerInvoice')
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
            ->add('deliveryHeader')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SaleInvoiceHeader::class,
        ));
    }
}
