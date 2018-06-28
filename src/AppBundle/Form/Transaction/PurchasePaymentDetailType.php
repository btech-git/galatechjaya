<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\Common\Collections\Criteria;
use AppBundle\Entity\Transaction\PurchasePaymentDetail;
use AppBundle\Entity\Transaction\PurchaseReceiptHeader;
use AppBundle\Entity\Master\Account;
use LibBundle\Form\Type\EntityHiddenType;

class PurchasePaymentDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount')
            ->add('memo')
            ->add('account', EntityType::class, array(
                'class' => Account::class,
                'choices' => $options['accountRepository']->findBy(array('isCashOrBank' => true), array('code' => Criteria::ASC)),
            ))
            ->add('purchaseReceiptHeader', EntityHiddenType::class, array('class' => PurchaseReceiptHeader::class))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PurchasePaymentDetail::class,
        ));
        $resolver->setRequired(array('accountRepository'));
    }
}
