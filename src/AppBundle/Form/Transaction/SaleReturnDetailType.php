<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Transaction\SaleReturnDetail;
use LibBundle\Form\Type\EntityHiddenType;
use AppBundle\Entity\Transaction\SaleInvoiceDetail;

class SaleReturnDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity')
            ->add('saleInvoiceDetail', EntityHiddenType::class, array('class' => SaleInvoiceDetail::class))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SaleReturnDetail::class,
        ));
    }
}
