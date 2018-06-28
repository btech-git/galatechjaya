<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Transaction\PurchaseInvoiceDetail;
use AppBundle\Entity\Transaction\ReceiveDetail;
use LibBundle\Form\Type\EntityHiddenType;

class PurchaseInvoiceDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('receiveDetail', EntityHiddenType::class, array('class' => ReceiveDetail::class))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PurchaseInvoiceDetail::class,
        ));
    }
}
