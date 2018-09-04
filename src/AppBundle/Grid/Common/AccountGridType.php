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
use AppBundle\Entity\Master\AccountCategory;
use AppBundle\Entity\Master\Account;

class AccountGridType extends DataGridType
{
    /**
     * @param WidgetsBuilder $builder
     * @param array $options
     */
    public function buildWidgets(WidgetsBuilder $builder, array $options)
    {
        $em = $options['em'];
        $accountCategories = $em->getRepository(AccountCategory::class)->findAll();
        $accountCategoryLabelModifier = function($accountCategory) { return $accountCategory->getName(); };

        $builder->searchWidget()
            ->addGroup('account')
                ->setEntityName(Account::class)
                ->addField('code')
                    ->addOperator(ContainNonEmptyType::class)
                ->addField('name')
                    ->addOperator(EqualNonEmptyType::class)
                ->addField('accountCategory')
                    ->setDataTransformer(new EntityTransformer($em, AccountCategory::class))
                    ->addOperator(EqualNonEmptyType::class)
                        ->getInput(1)
                            ->setListData($accountCategories, $accountCategoryLabelModifier)
        ;

        $builder->sortWidget()
            ->addGroup('account')
                ->addField('code')
                    ->addOperator(SortBlankType::class)
                    ->addOperator(AscendingType::class)
                    ->addOperator(DescendingType::class)
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
            $operator::search($criteria[$group], $field, $values);
        });

        $builder->processSort(function($operator, $field, $group) use ($criteria) {
            $operator::sort($criteria[$group], $field);
        });

        $builder->processPage($repository->count($criteria['account'], $associations), function($offset, $size) use ($criteria) {
            $criteria['account']->setMaxResults($size);
            $criteria['account']->setFirstResult($offset);
        });
        
        $objects = $repository->match($criteria['account'], $associations);

        $builder->setData($objects);
    }

    private function getSpecifications(array $options)
    {
        $names = array('account');
        $criteria = array();
        foreach ($names as $name) {
            $criteria[$name] = Criteria::create();
        }

        $associations = array();

        if (array_key_exists('form', $options)) {
//            $expr = Criteria::expr();
            switch ($options['form']) {
                case 'sale_invoice_downpayment':
                    $associations['saleInvoiceDownpayment']['merge'] = true;
                    break;
                case 'sale_payment_detail':
                    $associations['salePaymentDetail']['merge'] = true;
                    break;
                case 'purchase_payment_detail':
                    $associations['purchasePaymentDetail']['merge'] = true;
                    break;
                case 'expense_header':
                    $associations['expenseHeader']['merge'] = true;
                    break;
                case 'expense_detail':
                    $associations['expenseDetail']['merge'] = true;
                    break;
                case 'deposit_header':
                    $associations['depositHeader']['merge'] = true;
                    break;
                case 'deposit_detail':
                    $associations['depositDetail']['merge'] = true;
                    break;
                case 'journal_voucher_detail':
                    $associations['journalVoucherDetail']['merge'] = true;
                    break;
            }
        }

        return array($criteria, $associations);
    }
}
