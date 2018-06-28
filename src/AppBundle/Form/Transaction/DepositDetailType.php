<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Transaction\DepositDetail;
use LibBundle\Form\Type\EntityHiddenType;
use AppBundle\Entity\Master\Account;

class DepositDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('amount')
            ->add('account', EntityHiddenType::class, array('class' => Account::class))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => DepositDetail::class,
        ));
    }
}
