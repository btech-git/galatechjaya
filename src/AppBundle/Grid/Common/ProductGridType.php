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
use LibBundle\Grid\Transformer\EntityTransformer;
use AppBundle\Entity\Master\ProductCategory;
use AppBundle\Entity\Master\Product;

class ProductGridType extends DataGridType
{
    /**
     * @param WidgetsBuilder $builder
     * @param array $options
     */
    public function buildWidgets(WidgetsBuilder $builder, array $options)
    {
        $em = $options['em'];
        $productCategories = $em->getRepository(ProductCategory::class)->findAll();
        $productCategoryLabelModifier = function($productCategory) { return $productCategory->getName(); };

        $builder->searchWidget()
            ->addGroup('product')
                ->setEntityName(Product::class)
                ->addField('name')
                    ->addOperator(ContainNonEmptyType::class)
                ->addField('size')
                    ->addOperator(EqualNonEmptyType::class)
            ->addGroup('productCategory')
                ->setEntityName(ProductCategory::class)
                ->addField('name')
                    ->addOperator(ContainNonEmptyType::class)
        ;

        $builder->sortWidget()
            ->addGroup('product')
                ->addField('name')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
                ->addField('size')
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
            if ($group === 'productCategory' && $field === 'name' && $operator === ContainNonEmptyType::class && $values[0] !== null && $values[0] !== '') {
                $associations['productCategory']['merge'] = true;
            }
            $operator::search($criteria[$group], $field, $values);
        });

        $builder->processSort(function($operator, $field, $group) use ($criteria) {
            $operator::sort($criteria[$group], $field);
        });

        $builder->processPage($repository->count($criteria['product'], $associations), function($offset, $size) use ($criteria) {
            $criteria['product']->setMaxResults($size);
            $criteria['product']->setFirstResult($offset);
        });
        
        $objects = $repository->match($criteria['product'], $associations);

        $builder->setData($objects);
    }

    private function getSpecifications(array $options)
    {
        $names = array('product', 'productCategory');
        $criteria = array();
        foreach ($names as $name) {
            $criteria[$name] = Criteria::create();
        }

        $associations = array(
            'productCategory' => array('criteria' => $criteria['productCategory']),
        );

        if (array_key_exists('form', $options)) {
            switch ($options['form']) {
                case 'adjustment_stock_detail':
                    $associations['adjustmentStockDetail']['merge'] = true;
                    break;
                case 'purchase_order_detail':
                    $associations['purchaseOrderDetail']['merge'] = true;
                    break;
                case 'sale_invoice_detail':
                    $associations['saleInvoiceDetail']['merge'] = true;
                    break;
                case 'transfer_detail':
                    $associations['transferDetail']['merge'] = true;
                    break;
            }
        }

        return array($criteria, $associations);
    }
}
