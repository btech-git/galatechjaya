<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Transaction\AdjustmentStockDetail;
use AppBundle\Entity\Master\Product;
use LibBundle\Form\Type\EntityHiddenType;

class AdjustmentStockDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantityAdjustment')
            ->add('product', EntityHiddenType::class, array('class' => Product::class))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => AdjustmentStockDetail::class,
        ));
    }
}
