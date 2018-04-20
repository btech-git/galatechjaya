<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Transaction\AdjustmentStockDetail;

class AdjustmentStockDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantityCurrent')
            ->add('quantityAdjustment')
            ->add('quantityDifference')
            ->add('product')
            ->add('adjustmentStockHeader')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => AdjustmentStockDetail::class,
        ));
    }
}
