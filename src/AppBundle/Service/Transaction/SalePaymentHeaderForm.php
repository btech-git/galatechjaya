<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\SalePaymentHeader;
use AppBundle\Entity\Report\JournalLedger;
use AppBundle\Repository\Transaction\SalePaymentHeaderRepository;
use AppBundle\Repository\Report\JournalLedgerRepository;

class SalePaymentHeaderForm
{
    private $salePaymentHeaderRepository;
    private $journalLedgerRepository;
    
    public function __construct(SalePaymentHeaderRepository $salePaymentHeaderRepository, JournalLedgerRepository $journalLedgerRepository)
    {
        $this->salePaymentHeaderRepository = $salePaymentHeaderRepository;
        $this->journalLedgerRepository = $journalLedgerRepository;
    }
    
    public function initialize(SalePaymentHeader $salePaymentHeader, array $params = array())
    {
        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($salePaymentHeader->getId())) {
            $lastSalePaymentHeader = $this->salePaymentHeaderRepository->findRecentBy($year, $month);
            $currentSalePaymentHeader = ($lastSalePaymentHeader === null) ? $salePaymentHeader : $lastSalePaymentHeader;
            $salePaymentHeader->setCodeNumberToNext($currentSalePaymentHeader->getCodeNumber(), $year, $month);
            
            $salePaymentHeader->setStaffFirst($staff);
        }
        $salePaymentHeader->setStaffLast($staff);
    }
    
    public function finalize(SalePaymentHeader $salePaymentHeader, array $params = array())
    {
        foreach ($salePaymentHeader->getSalePaymentDetails() as $salePaymentDetail) {
            $salePaymentDetail->setSalePaymentHeader($salePaymentHeader);
        }
        $this->sync($salePaymentHeader);
    }
    
    private function sync(SalePaymentHeader $salePaymentHeader)
    {
        $salePaymentHeader->sync();
        
        $oldSalePaymentDetails = $this->salePaymentDetailRepository->findBySalePaymentHeader($salePaymentHeader);
        $newSalePaymentDetails = $salePaymentHeader->getSalePaymentDetails()->getValues();
        $saleReceiptHeaders = array();
        foreach ($oldSalePaymentDetails as $oldSalePaymentDetail) {
            $saleReceiptHeaders[] = $oldSalePaymentDetail->getSaleReceiptHeader();
        }
        foreach ($newSalePaymentDetails as $newSalePaymentDetail) {
            $saleReceiptHeaders[] = $newSalePaymentDetail->getSaleReceiptHeader();
        }
        $saleReceiptHeaderIds = array();
        foreach ($saleReceiptHeaders as $saleReceiptHeader) {
            $saleReceiptHeaderId = $saleReceiptHeader->getId();
            if (in_array($saleReceiptHeaderId, $saleReceiptHeaderIds)) {
                continue;
            }
            $saleReceiptHeaderIds[] = $saleReceiptHeaderId;
            
            $salePaymentDetails = $saleReceiptHeader->getSalePaymentDetails();
            foreach ($oldSalePaymentDetails as $oldSalePaymentDetail) {
                if (!in_array($oldSalePaymentDetail, $newSalePaymentDetails) && $oldSalePaymentDetail->getSaleReceiptHeader()->getId() === $saleReceiptHeaderId) {
                    $salePaymentDetails->removeElement($oldSalePaymentDetail);
                }
            }
            foreach ($newSalePaymentDetails as $newSalePaymentDetail) {
                if (!in_array($newSalePaymentDetail, $oldSalePaymentDetails) && $newSalePaymentDetail->getSaleReceiptHeader()->getId() === $saleReceiptHeaderId) {
                    $salePaymentDetails->add($newSalePaymentDetail);
                }
            }
            $totalPayment = 0.00;
            foreach ($salePaymentDetails as $salePaymentDetail) {
                $totalPayment += $salePaymentDetail->getAmount();
            }
            $saleReceiptHeader->setTotalPayment($totalPayment);
            $saleReceiptHeader->setRemaining($saleReceiptHeader->getGrandTotal() - $totalPayment);
        }
    }
    
    public function save(SalePaymentHeader $salePaymentHeader)
    {
        if (empty($salePaymentHeader->getId())) {
            ObjectPersister::save(function() use ($salePaymentHeader) {
                $this->salePaymentHeaderRepository->add($salePaymentHeader, array(
                    'salePaymentDetails' => array('add' => true),
                ));
                $this->markJournalLedgers($salePaymentHeader);
            });
        } else {
            ObjectPersister::save(function() use ($salePaymentHeader) {
                $this->salePaymentHeaderRepository->update($salePaymentHeader, array(
                    'salePaymentDetails' => array('add' => true, 'remove' => true),
                ));
                $this->markJournalLedgers($salePaymentHeader);
            });
        }
    }
    
    public function delete(SalePaymentHeader $salePaymentHeader)
    {
        $this->beforeDelete($salePaymentHeader);
        if (!empty($salePaymentHeader->getId())) {
            ObjectPersister::save(function() use ($salePaymentHeader) {
                $this->salePaymentHeaderRepository->remove($salePaymentHeader, array(
                    'salePaymentDetails' => array('remove' => true),
                ));
                $this->markJournalLedgers($salePaymentHeader);
            });
        }
    }
    
    public function isValidForDelete(SalePaymentHeader $salePaymentHeader)
    {
        $valid = $salePaymentHeader->getSalePaymentDetails()->isEmpty();
        
        return $valid;
    }
    
    protected function beforeDelete(SalePaymentHeader $salePaymentHeader)
    {
        $salePaymentHeader->getSalePaymentDetails()->clear();
        $this->sync($salePaymentHeader);
    }
    
    private function markJournalLedgers(SalePaymentHeader $salePaymentHeader)
    {
        $oldJournalLedgers = $this->journalLedgerRepository->findBy(array(
            'transactionType' => JournalLedger::TRANSACTION_TYPE_RECEIVABLE_PAYMENT,
            'codeNumberYear' => $salePaymentHeader->getCodeNumberYear(),
            'codeNumberMonth' => $salePaymentHeader->getCodeNumberMonth(),
            'codeNumberOrdinal' => $salePaymentHeader->getCodeNumberOrdinal(),
        ));
        $this->journalLedgerRepository->remove($oldJournalLedgers);
        foreach ($salePaymentHeader->getSalePaymentDetails() as $salePaymentDetail) {
            if ($salePaymentDetail->getAmount() > 0.00) {
                $journalLedgerCredit = new JournalLedger();
                $journalLedgerCredit->setCodeNumber($salePaymentHeader->getCodeNumber());
                $journalLedgerCredit->setTransactionDate($salePaymentHeader->getTransactionDate());
                $journalLedgerCredit->setTransactionType(JournalLedger::TRANSACTION_TYPE_RECEIVABLE_PAYMENT);
                $journalLedgerCredit->setTransactionSubject($salePaymentHeader->getCustomer());
                $journalLedgerCredit->setNote($salePaymentHeader->getNote());
                $journalLedgerCredit->setDebit(0.00);
                $journalLedgerCredit->setCredit($salePaymentDetail->getAmount());
                $journalLedgerCredit->setAccount($salePaymentHeader->getCustomer()->getAccountReceivable());
                $journalLedgerCredit->setStaff($salePaymentHeader->getStaffFirst());
                $this->journalLedgerRepository->add($journalLedgerCredit);

                $journalLedgerDebit = new JournalLedger();
                $journalLedgerDebit->setCodeNumber($salePaymentHeader->getCodeNumber());
                $journalLedgerDebit->setTransactionDate($salePaymentHeader->getTransactionDate());
                $journalLedgerDebit->setTransactionType(JournalLedger::TRANSACTION_TYPE_RECEIVABLE_PAYMENT);
                $journalLedgerDebit->setTransactionSubject($salePaymentHeader->getCustomer());
                $journalLedgerDebit->setNote($salePaymentHeader->getNote());
                $journalLedgerDebit->setDebit($salePaymentDetail->getAmount());
                $journalLedgerDebit->setCredit(0.00);
                $journalLedgerDebit->setAccount($salePaymentDetail->getAccount());
                $journalLedgerDebit->setStaff($salePaymentHeader->getStaffFirst());
                $this->journalLedgerRepository->add($journalLedgerDebit);
            }
        }
    }
}