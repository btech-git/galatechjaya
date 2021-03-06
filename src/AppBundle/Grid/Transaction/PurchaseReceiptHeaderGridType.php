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
use AppBundle\Entity\Transaction\PurchaseReceiptHeader;

class PurchaseReceiptHeaderGridType extends DataGridType
{
    public function buildWidgets(WidgetsBuilder $builder, array $options)
    {
        $builder->searchWidget()
            ->addGroup('purchaseReceiptHeader')
                ->setEntityName(PurchaseReceiptHeader::class)
                ->addField('transactionDate')
                    ->addOperator(SearchBlankType::class)
                    ->addOperator(EqualType::class)
                        ->getInput(1)
                            ->setAttributes(array('data-pick' => 'date'))
                ->addField('note')
                    ->addOperator(SearchBlankType::class)
                    ->addOperator(EqualType::class)
                    ->addOperator(ContainType::class)
                ->addField('grandTotal')
                    ->addOperator(SearchBlankType::class)
                    ->addOperator(EqualType::class)
                ->addField('totalPayment')
                    ->addOperator(SearchBlankType::class)
                    ->addOperator(EqualType::class)
                ->addField('remaining')
                    ->addOperator(SearchBlankType::class)
                    ->addOperator(EqualType::class)
        ;

        $builder->sortWidget()
            ->addGroup('purchaseReceiptHeader')
                ->addField('transactionDate')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('note')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('grandTotal')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('totalPayment')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('remaining')
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

        $builder->processPage($repository->count($criteria['purchaseReceiptHeader'], $associations), function($offset, $size) use ($criteria) {
            $criteria['purchaseReceiptHeader']->setMaxResults($size);
            $criteria['purchaseReceiptHeader']->setFirstResult($offset);
        });
        
        $objects = $repository->match($criteria['purchaseReceiptHeader'], $associations);

        $builder->setData($objects);
    }

    private function getSpecifications(array $options)
    {
        $names = array('purchaseReceiptHeader', 'supplier');
        $criteria = array();
        foreach ($names as $name) {
            $criteria[$name] = Criteria::create();
        }

        $associations = array(
            'supplier' => array('criteria' => $criteria['supplier']),
        );

        if (array_key_exists('form', $options)) {
            switch ($options['form']) {
                
            }
        }

        return array($criteria, $associations);
    }
}
