<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\DepositHeader;
use AppBundle\Entity\Report\JournalLedger;
use AppBundle\Repository\Transaction\DepositHeaderRepository;
use AppBundle\Repository\Report\JournalLedgerRepository;

class DepositHeaderForm
{
    private $depositHeaderRepository;
    private $journalLedgerRepository;
    
    public function __construct(DepositHeaderRepository $depositHeaderRepository, JournalLedgerRepository $journalLedgerRepository)
    {
        $this->depositHeaderRepository = $depositHeaderRepository;
        $this->journalLedgerRepository = $journalLedgerRepository;
    }
    
    public function initialize(DepositHeader $depositHeader, array $params = array())
    {
        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($depositHeader->getId())) {
            $lastDepositHeader = $this->depositHeaderRepository->findRecentBy($year, $month);
            $currentDepositHeader = ($lastDepositHeader === null) ? $depositHeader : $lastDepositHeader;
            $depositHeader->setCodeNumberToNext($currentDepositHeader->getCodeNumber(), $year, $month);
            
            $depositHeader->setStaff($staff);
        }
    }
    
    public function finalize(DepositHeader $depositHeader, array $params = array())
    {
        foreach ($depositHeader->getDepositDetails() as $depositDetail) {
            $depositDetail->setDepositHeader($depositHeader);
        }
        $this->sync($depositHeader);
    }
    
    private function sync(DepositHeader $depositHeader)
    {
        $depositHeader->sync();
    }
    
    public function save(DepositHeader $depositHeader)
    {
        if (empty($depositHeader->getId())) {
            ObjectPersister::save(function() use ($depositHeader) {
                $this->depositHeaderRepository->add($depositHeader, array(
                    'depositDetails' => array('add' => true),
                ));
                $this->markJournalLedgers($depositHeader, true);
            });
        } else {
            ObjectPersister::save(function() use ($depositHeader) {
                $this->depositHeaderRepository->update($depositHeader, array(
                    'depositDetails' => array('add' => true, 'remove' => true),
                ));
                $this->markJournalLedgers($depositHeader, true);
            });
        }
    }
    
    public function delete(DepositHeader $depositHeader)
    {
        $this->beforeDelete($depositHeader);
        if (!empty($depositHeader->getId())) {
            ObjectPersister::save(function() use ($depositHeader) {
                $this->depositHeaderRepository->remove($depositHeader, array(
                    'depositDetails' => array('remove' => true),
                ));
                $this->markJournalLedgers($depositHeader, false);
            });
        }
    }
    
    protected function beforeDelete(DepositHeader $depositHeader)
    {
        $depositHeader->getDepositDetails()->clear();
        $this->sync($depositHeader);
    }
    
    private function markJournalLedgers(DepositHeader $depositHeader, $addForHeader)
    {
        $oldJournalLedgers = $this->journalLedgerRepository->findBy(array(
            'transactionType' => JournalLedger::TRANSACTION_TYPE_DEPOSIT,
            'codeNumberYear' => $depositHeader->getCodeNumberYear(),
            'codeNumberMonth' => $depositHeader->getCodeNumberMonth(),
            'codeNumberOrdinal' => $depositHeader->getCodeNumberOrdinal(),
        ));
        $this->journalLedgerRepository->remove($oldJournalLedgers);
        foreach ($depositHeader->getDepositDetails() as $depositDetail) {
            if ($depositDetail->getAmount() > 0.00) {
                $journalLedgerCredit = new JournalLedger();
                $journalLedgerCredit->setCodeNumber($depositHeader->getCodeNumber());
                $journalLedgerCredit->setTransactionDate($depositHeader->getTransactionDate());
                $journalLedgerCredit->setTransactionType(JournalLedger::TRANSACTION_TYPE_DEPOSIT);
                $journalLedgerCredit->setTransactionSubject($depositDetail->getDescription());
                $journalLedgerCredit->setNote($depositHeader->getNote());
                $journalLedgerCredit->setDebit(0.00);
                $journalLedgerCredit->setCredit($depositDetail->getAmount());
                $journalLedgerCredit->setAccount($depositDetail->getAccount());
                $journalLedgerCredit->setStaff($depositHeader->getStaff());
                $this->journalLedgerRepository->add($journalLedgerCredit);
                
                $journalLedgerDebit = new JournalLedger();
                $journalLedgerDebit->setCodeNumber($depositHeader->getCodeNumber());
                $journalLedgerDebit->setTransactionDate($depositHeader->getTransactionDate());
                $journalLedgerDebit->setTransactionType(JournalLedger::TRANSACTION_TYPE_DEPOSIT);
                $journalLedgerDebit->setTransactionSubject($depositDetail->getDescription());
                $journalLedgerDebit->setNote($depositHeader->getNote());
                $journalLedgerDebit->setDebit($depositDetail->getAmount());
                $journalLedgerDebit->setCredit(0.00);
                $journalLedgerDebit->setAccount($depositHeader->getAccount());
                $journalLedgerDebit->setStaff($depositHeader->getStaff());
                $this->journalLedgerRepository->add($journalLedgerDebit);
            }
        }
    }
}