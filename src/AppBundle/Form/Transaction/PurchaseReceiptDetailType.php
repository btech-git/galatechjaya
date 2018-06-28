<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Transaction\PurchaseReceiptDetail;
use AppBundle\Entity\Transaction\PurchaseInvoiceHeader;
use LibBundle\Form\Type\EntityHiddenType;

class PurchaseReceiptDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('memo')
            ->add('purchaseInvoiceHeader', EntityHiddenType::class, array('class' => PurchaseInvoiceHeader::class))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PurchaseReceiptDetail::class,
        ));
    }
}
