<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Transaction\SaleInvoiceDetail;
use LibBundle\Form\Type\EntityHiddenType;
use AppBundle\Entity\Master\Product;

class SaleInvoiceDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('itemName')
            ->add('quantity')
            ->add('unitPrice')
            ->add('discount')
            ->add('product', EntityHiddenType::class, array('class' => Product::class))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SaleInvoiceDetail::class,
        ));
    }
}
