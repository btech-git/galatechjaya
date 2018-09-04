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
use LibBundle\Grid\SearchOperator\EqualNonEmptyType;
use LibBundle\Grid\SearchOperator\ContainNonEmptyType;
use AppBundle\Entity\Transaction\PurchaseInvoiceHeader;
use AppBundle\Entity\Master\Supplier;

class PurchaseInvoiceHeaderGridType extends DataGridType
{
    /**
     * @param WidgetsBuilder $builder
     * @param array $options
     */
    public function buildWidgets(WidgetsBuilder $builder, array $options)
    {
        $months = array_flip(array(1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'));

        $builder->searchWidget()
            ->addGroup('purchaseInvoiceHeader')
                ->setEntityName(PurchaseInvoiceHeader::class)
                ->addField('codeNumber')
                    ->setReferences(array('codeNumberOrdinal', 'codeNumberMonth', 'codeNumberYear'))
                    ->addOperator(EqualNonEmptyType::class)
                        ->getInput(1, 1)
                            ->setPlaceHolder('Number')
                            ->setAttributes(array('size' => 5, 'maxlength' => 4))
                        ->getInput(1, 2)
                            ->setListData($months)
                            ->setPlaceHolder('Month')
                        ->getInput(1, 3)
                            ->setPlaceHolder('Year')
                            ->setAttributes(array('size' => 3, 'maxlength' => 2))
                ->addField('transactionDate')
                    ->addOperator(EqualNonEmptyType::class)
                        ->getInput(1)
                            ->setAttributes(array('data-pick' => 'date'))
            ->addGroup('supplier')
                ->setEntityName(Supplier::class)
                ->addField('name')
                    ->addOperator(ContainNonEmptyType::class)
        ;

        $builder->sortWidget()
            ->addGroup('purchaseInvoiceHeader')
                ->addField('codeNumber')
                    ->setReferences(array('codeNumberYear', 'codeNumberMonth', 'codeNumberOrdinal'))
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('transactionDate')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
            ->addGroup('supplier')
                ->addField('name')
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
//            if ($group === 'supplier' && $field === 'name' && $operator === ContainNonEmptyType::class && $values[0] !== null && $values[0] !== '') {
//                $associations['receiveHeader']['associations']['purchaseOrderHeader']['associations']['supplier']['merge'] = true;
//            }
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
            'purchaseReceiptDetails' => array('criteria' => null),
            'purchaseReturnHeaders' => array('criteria' => null),
            'receiveHeader' => array('criteria' => null, 'associations' => array(
                'purchaseOrderHeader' => array('criteria' => null, 'associations' => array(
                    'supplier' => array('criteria' => $criteria['supplier'], 'merge' => true),
                )),
            )),
        );

        if (array_key_exists('form', $options)) {
            $expr = Criteria::expr();
            switch ($options['form']) {
                case 'purchase_receipt_header':
                    if (array_key_exists('supplier_id', $options['options'])) {
                        $criteria['supplier']->andWhere($expr->eq('id', $options['options']['supplier_id']));
                    }
                    $associations['purchaseReceiptDetails']['merge'] = false;
                    break;
                case 'purchase_return_header':
                    $associations['purchaseReturnHeaders']['merge'] = false;
                    break;
            }
        }

        return array($criteria, $associations);
    }
}
