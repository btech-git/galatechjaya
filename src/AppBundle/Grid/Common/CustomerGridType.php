<?php

namespace AppBundle\Grid\Common;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectRepository;
use LibBundle\Grid\DataGridType;
use LibBundle\Grid\WidgetsBuilder;
use LibBundle\Grid\DataBuilder;
use LibBundle\Grid\SortOperator\BlankType as SortBlankType;
use LibBundle\Grid\SortOperator\AscendingType;
use LibBundle\Grid\SortOperator\DescendingType;
use LibBundle\Grid\SearchOperator\ContainNonEmptyType;
use AppBundle\Entity\Master\Customer;

class CustomerGridType extends DataGridType
{
    /**
     * @param WidgetsBuilder $builder
     * @param array $options
     */
    public function buildWidgets(WidgetsBuilder $builder, array $options)
    {
        $builder->searchWidget()
            ->addGroup('customer')
                ->setEntityName(Customer::class)
                ->addField('name')
                    ->addOperator(ContainNonEmptyType::class)
                ->addField('contactPerson')
                    ->addOperator(ContainNonEmptyType::class)
        ;

        $builder->sortWidget()
            ->addGroup('customer')
                ->addField('name')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('contactPerson')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
        ;

        $builder->pageWidget()
            ->addPageSizeField()
                ->addItems(10, 25, 50, 100)
            ->addPageNumField()
        ;
    }

    public function buildData(DataBuilder $builder, ObjectRepository $repository, array $options)
    {
        list($criteria, $associations) = $this->getSpecifications($options);

        $builder->processSearch(function($values, $operator, $field, $group) use ($criteria, &$associations) {
            $operator::search($criteria[$group], $field, $values);
        });

        $builder->processSort(function($operator, $field, $group) use ($criteria) {
            $operator::sort($criteria[$group], $field);
        });

        $builder->processPage($repository->count($criteria['customer'], $associations), function($offset, $size) use ($criteria) {
            $criteria['customer']->setMaxResults($size);
            $criteria['customer']->setFirstResult($offset);
        });
        
        $objects = $repository->match($criteria['customer'], $associations);

        $builder->setData($objects);
    }

    private function getSpecifications(array $options)
    {
        $names = array('customer');
        $criteria = array();
        foreach ($names as $name) {
            $criteria[$name] = Criteria::create();
        }

        $associations = array();

        if (array_key_exists('form', $options)) {
            switch ($options['form']) {
                case 'sale_receipt_header':
                    $associations['saleReceiptHeader']['merge'] = true;
                    break;
                case 'sale_payment_header':
                    $associations['salePaymentHeader']['merge'] = true;
                    break;
                case 'sale_invoice_header':
                    $associations['saleInvoiceHeader']['merge'] = true;
                    break;
            }
        }
        
        return array($criteria, $associations);
    }
}
