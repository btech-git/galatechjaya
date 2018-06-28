<?php

namespace AppBundle\Service\Transaction;

use AppBundle\Entity\Transaction\ExpenseHeader;
use AppBundle\Repository\Transaction\ExpenseHeaderRepository;

class ExpenseHeaderForm
{
    private $expenseHeaderRepository;
    
    public function __construct(ExpenseHeaderRepository $expenseHeaderRepository)
    {
        $this->expenseHeaderRepository = $expenseHeaderRepository;
    }
    
    public function initialize(ExpenseHeader $expenseHeader, array $params = array())
    {
        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($expenseHeader->getId())) {
            $lastExpenseHeader = $this->expenseHeaderRepository->findRecentBy($year, $month);
            $currentExpenseHeader = ($lastExpenseHeader === null) ? $expenseHeader : $lastExpenseHeader;
            $expenseHeader->setCodeNumberToNext($currentExpenseHeader->getCodeNumber(), $year, $month);
            
            $expenseHeader->setStaff($staff);
        }
    }
    
    public function finalize(ExpenseHeader $expenseHeader, array $params = array())
    {
        foreach ($expenseHeader->getExpenseDetails() as $expenseDetail) {
            $expenseDetail->setExpenseHeader($expenseHeader);
        }
        $this->sync($expenseHeader);
    }
    
    private function sync(ExpenseHeader $expenseHeader)
    {
        $expenseHeader->sync();
    }
    
    public function save(ExpenseHeader $expenseHeader)
    {
        if (empty($expenseHeader->getId())) {
            $this->expenseHeaderRepository->add($expenseHeader, array(
                'expenseDetails' => array('add' => true),
            ));
        } else {
            $this->expenseHeaderRepository->update($expenseHeader, array(
                'expenseDetails' => array('add' => true, 'remove' => true),
            ));
        }
    }
    
    public function delete(ExpenseHeader $expenseHeader)
    {
        $this->beforeDelete($expenseHeader);
        if (!empty($expenseHeader->getId())) {
            $this->expenseHeaderRepository->remove($expenseHeader, array(
                'expenseDetails' => array('remove' => true),
            ));
        }
    }
    
    protected function beforeDelete(ExpenseHeader $expenseHeader)
    {
        $expenseHeader->getExpenseDetails()->clear();
        $this->sync($expenseHeader);
    }
}