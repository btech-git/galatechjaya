<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\ExpenseHeader;
use AppBundle\Entity\Report\JournalLedger;
use AppBundle\Repository\Transaction\ExpenseHeaderRepository;
use AppBundle\Repository\Report\JournalLedgerRepository;

class ExpenseHeaderForm
{
    private $expenseHeaderRepository;
    private $journalLedgerRepository;
    
    public function __construct(ExpenseHeaderRepository $expenseHeaderRepository, JournalLedgerRepository $journalLedgerRepository)
    {
        $this->expenseHeaderRepository = $expenseHeaderRepository;
        $this->journalLedgerRepository = $journalLedgerRepository;
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
            ObjectPersister::save(function() use ($expenseHeader) {
                $this->expenseHeaderRepository->add($expenseHeader, array(
                    'expenseDetails' => array('add' => true),
                ));
                $this->markJournalLedgers($expenseHeader, true);
            });
        } else {
            ObjectPersister::save(function() use ($expenseHeader) {
                $this->expenseHeaderRepository->update($expenseHeader, array(
                    'expenseDetails' => array('add' => true, 'remove' => true),
                ));
                $this->markJournalLedgers($expenseHeader, true);
            });
        }
    }
    
    public function delete(ExpenseHeader $expenseHeader)
    {
        $this->beforeDelete($expenseHeader);
        if (!empty($expenseHeader->getId())) {
            ObjectPersister::save(function() use ($expenseHeader) {
                $this->expenseHeaderRepository->remove($expenseHeader, array(
                    'expenseDetails' => array('remove' => true),
                ));
                $this->markJournalLedgers($expenseHeader, true);
            });
        }
    }
    
    protected function beforeDelete(ExpenseHeader $expenseHeader)
    {
        $expenseHeader->getExpenseDetails()->clear();
        $this->sync($expenseHeader);
    }
    
    private function markJournalLedgers(ExpenseHeader $expenseHeader, $addForHeader)
    {
        $oldJournalLedgers = $this->journalLedgerRepository->findBy(array(
            'transactionType' => JournalLedger::TRANSACTION_TYPE_EXPENSE,
            'codeNumberYear' => $expenseHeader->getCodeNumberYear(),
            'codeNumberMonth' => $expenseHeader->getCodeNumberMonth(),
            'codeNumberOrdinal' => $expenseHeader->getCodeNumberOrdinal(),
        ));
        $this->journalLedgerRepository->remove($oldJournalLedgers);
        foreach ($expenseHeader->getExpenseDetails() as $expenseDetail) {
            if ($expenseDetail->getAmount() > 0.00) {
                $journalLedgerCredit = new JournalLedger();
                $journalLedgerCredit->setCodeNumber($expenseHeader->getCodeNumber());
                $journalLedgerCredit->setTransactionDate($expenseHeader->getTransactionDate());
                $journalLedgerCredit->setTransactionType(JournalLedger::TRANSACTION_TYPE_EXPENSE);
                $journalLedgerCredit->setTransactionSubject($expenseDetail->getDescription());
                $journalLedgerCredit->setNote($expenseHeader->getNote());
                $journalLedgerCredit->setDebit(0.00);
                $journalLedgerCredit->setCredit($expenseDetail->getAmount());
                $journalLedgerCredit->setAccount($expenseHeader->getAccount());
                $journalLedgerCredit->setStaff($expenseHeader->getStaff());
                $this->journalLedgerRepository->add($journalLedgerCredit);
                
                $journalLedgerDebit = new JournalLedger();
                $journalLedgerDebit->setCodeNumber($expenseHeader->getCodeNumber());
                $journalLedgerDebit->setTransactionDate($expenseHeader->getTransactionDate());
                $journalLedgerDebit->setTransactionType(JournalLedger::TRANSACTION_TYPE_EXPENSE);
                $journalLedgerDebit->setTransactionSubject($expenseDetail->getDescription());
                $journalLedgerDebit->setNote($expenseHeader->getNote());
                $journalLedgerDebit->setDebit($expenseDetail->getAmount());
                $journalLedgerDebit->setCredit(0.00);
                $journalLedgerDebit->setAccount($expenseDetail->getAccount());
                $journalLedgerDebit->setStaff($expenseHeader->getStaff());
                $this->journalLedgerRepository->add($journalLedgerDebit);
            }
        }
    }
}