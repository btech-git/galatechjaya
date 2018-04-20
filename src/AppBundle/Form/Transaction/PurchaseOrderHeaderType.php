<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Transaction\PurchaseOrderHeader;

class PurchaseOrderHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate')
            ->add('note')
            ->add('subTotal')
            ->add('discount')
            ->add('taxNominal')
            ->add('shippingFee')
            ->add('grandTotal')
            ->add('isTax')
            ->add('staffFirst')
            ->add('staffLast')
            ->add('supplier')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PurchaseOrderHeader::class,
        ));
    }
}
