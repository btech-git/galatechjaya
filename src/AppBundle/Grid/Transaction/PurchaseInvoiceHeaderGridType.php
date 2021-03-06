<?php

namespace AppBundle\Grid\Transaction;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectRepository;
use LibBundle\Grid\DataGridType;
use LibBundle\Grid\WidgetsBuilder;
use LibBundle\Grid\DataBuilder;
use LibBundle\Grid\SortOperator\BlankType as SortBlankType;
use LibBundle\Grid\SortOperator\AscendingType;
use LibBundle\Grid\SortOperator\DescendingType;
use LibBundle\Grid\SearchOperator\BlankType as SearchBlankType;
use LibBundle\Grid\SearchOperator\EqualType;
use LibBundle\Grid\SearchOperator\ContainType;
use AppBundle\Entity\Transaction\PurchaseInvoiceHeader;

class PurchaseInvoiceHeaderGridType extends DataGridType
{
    public function buildWidgets(WidgetsBuilder $builder, array $options)
    {
        $builder->searchWidget()
            ->addGroup('purchaseInvoiceHeader')
                ->setEntityName(PurchaseInvoiceHeader::class)
                ->addField('transactionDate')
                    ->addOperator(SearchBlankType::class)
                    ->addOperator(EqualType::class)
                        ->getInput(1)
                            ->setAttributes(array('data-pick' => 'date'))
                ->addField('taxInvoiceCode')
                    ->addOperator(SearchBlankType::class)
                    ->addOperator(EqualType::class)
                    ->addOperator(ContainType::class)
                ->addField('supplierInvoice')
                    ->addOperator(SearchBlankType::class)
                    ->addOperator(EqualType::class)
                    ->addOperator(ContainType::class)
                ->addField('subTotal')
                    ->addOperator(SearchBlankType::class)
                    ->addOperator(EqualType::class)
                ->addField('discount')
                    ->addOperator(SearchBlankType::class)
                    ->addOperator(EqualType::class)
                ->addField('taxNominal')
                    ->addOperator(SearchBlankType::class)
                    ->addOperator(EqualType::class)
                ->addField('shippingFee')
                    ->addOperator(SearchBlankType::class)
                    ->addOperator(EqualType::class)
                ->addField('totalReturn')
                    ->addOperator(SearchBlankType::class)
                    ->addOperator(EqualType::class)
                ->addField('grandTotal')
                    ->addOperator(SearchBlankType::class)
                    ->addOperator(EqualType::class)
                ->addField('note')
                    ->addOperator(SearchBlankType::class)
                    ->addOperator(EqualType::class)
                    ->addOperator(ContainType::class)
                ->addField('isTax')
                    ->addOperator(SearchBlankType::class)
                    ->addOperator(EqualType::class)
                        ->getInput(1)
                            ->setListData(array('Yes' => true, 'No' => false))
        ;

        $builder->sortWidget()
            ->addGroup('purchaseInvoiceHeader')
                ->addField('transactionDate')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('taxInvoiceCode')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('supplierInvoice')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('subTotal')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('discount')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('taxNominal')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('shippingFee')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('totalReturn')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('grandTotal')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('note')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('isTax')
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

        $builder->processPage($repository->count($criteria['purchaseInvoiceHeader'], $associations), function($offset, $size) use ($criteria) {
            $criteria['purchaseInvoiceHeader']->setMaxResults($size);
            $criteria['purchaseInvoiceHeader']->setFirstResult($offset);
        });
        
        $objects = $repository->match($criteria['purchaseInvoiceHeader'], $associations);

        $builder->setData($objects);
    }

    private function getSpecifications(array $options)
    {
        $names = array('purchaseInvoiceHeader', 'supplier');
        $criteria = array();
        foreach ($names as $name) {
            $criteria[$name] = Criteria::create();
        }

        $associations = array(
            'receiveHeader' => array('criteria' => null, 'associations' => array(
                'purchaseOrderHeader' => array('criteria' => null, 'associations' => array(
                    'supplier' => array('criteria' => $criteria['supplier']),
                )),
            )),
        );

        if (array_key_exists('form', $options)) {
            switch ($options['form']) {
                
            }
        }

        return array($criteria, $associations);
    }
}
