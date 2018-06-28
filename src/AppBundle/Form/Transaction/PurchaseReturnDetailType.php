<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Transaction\PurchaseReturnDetail;
use AppBundle\Entity\Transaction\PurchaseInvoiceDetail;
use LibBundle\Form\Type\EntityHiddenType;

class PurchaseReturnDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity')
            ->add('purchaseInvoiceDetail', EntityHiddenType::class, array('class' => PurchaseInvoiceDetail::class))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PurchaseReturnDetail::class,
        ));
    }
}
