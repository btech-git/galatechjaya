<?php

namespace AppBundle\Form\Transaction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use LibBundle\Form\Type\EntityTextType;
use AppBundle\Entity\Transaction\SaleCheque;
use AppBundle\Entity\Transaction\SaleReceiptHeader;

class SaleChequeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionDate', DateType::class)
            ->add('dateDue', DateType::class)
            ->add('chequeNumber')
            ->add('amount')
            ->add('bank')
            ->add('note')
        ;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($options) {
                $saleCheque = $event->getData();
                $options['service']->initialize($saleCheque, $options['init']);
                $form = $event->getForm();
                $formOptions = array(
                    'class' => SaleReceiptHeader::class,
                );
                if (!empty($saleCheque->getId())) {
                    $formOptions['disabled'] = true;
                }
                $form->add('saleReceiptHeader', EntityTextType::class, $formOptions);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options) {
                $saleCheque = $event->getData();
                $options['service']->finalize($saleCheque);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SaleCheque::class,
        ));
        $resolver->setRequired(array('service', 'init'));
    }
}
