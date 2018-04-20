<?php

namespace AppBundle\Form\Master;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Master\Customer;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('officeAddress')
            ->add('officeCity')
            ->add('officeProvince')
            ->add('officeZipCode')
            ->add('phone')
            ->add('fax')
            ->add('mobilePhone')
            ->add('contactPerson')
            ->add('email')
            ->add('taxNumber')
            ->add('webPage')
            ->add('creditPaymentTerm')
            ->add('note')
            ->add('isActive')
            ->add('accountReceivable')
            ->add('accountDownpayment')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Customer::class,
        ));
    }
}
