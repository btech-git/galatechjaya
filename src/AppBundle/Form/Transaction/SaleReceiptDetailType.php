<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Transaction\SaleReceiptDetail;
use AppBundle\Entity\Transaction\SaleInvoiceHeader;
use LibBundle\Form\Type\EntityHiddenType;

class SaleReceiptDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('memo')
            ->add('saleInvoiceHeader', EntityHiddenType::class, array('class' => SaleInvoiceHeader::class))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SaleReceiptDetail::class,
        ));
    }
}
