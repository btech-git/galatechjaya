<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Transaction\ReceiveDetail;
use LibBundle\Form\Type\EntityHiddenType;
use AppBundle\Entity\Transaction\PurchaseOrderDetail;

class ReceiveDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity')
            ->add('purchaseOrderDetail', EntityHiddenType::class, array('class' => PurchaseOrderDetail::class))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ReceiveDetail::class,
        ));
    }
}
