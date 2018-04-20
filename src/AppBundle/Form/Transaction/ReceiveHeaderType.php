<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Transaction\ReceiveHeader;

class ReceiveHeaderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate')
            ->add('reference')
            ->add('note')
            ->add('staffFirst')
            ->add('staffLast')
            ->add('warehouse')
            ->add('purchaseOrderHeader')
            ->add('purchaseInvoiceHeader')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ReceiveHeader::class,
        ));
    }
}
