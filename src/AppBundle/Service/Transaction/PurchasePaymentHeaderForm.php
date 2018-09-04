<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use LibBundle\Doctrine\EntityRepository;
use AppBundle\Entity\Transaction\PurchasePaymentHeader;
use AppBundle\Entity\Report\JournalLedger;
use AppBundle\Repository\Transaction\PurchasePaymentHeaderRepository;
use AppBundle\Repository\Report\JournalLedgerRepository;

class PurchasePaymentHeaderForm
{
    private $purchasePaymentHeaderRepository;
    private $purchasePaymentDetailRepository;
    private $journalLedgerRepository;
    
    public function __construct(PurchasePaymentHeaderRepository $purchasePaymentHeaderRepository, EntityRepository $purchasePaymentDetailRepository, JournalLedgerRepository $journalLedgerRepository)
    {
        $this->purchasePaymentHeaderRepository = $purchasePaymentHeaderRepository;
        $this->purchasePaymentDetailRepository = $purchasePaymentDetailRepository;
        $this->journalLedgerRepository = $journalLedgerRepository;
    }
    
    public function initialize(PurchasePaymentHeader $purchasePaymentHeader, array $params = array())
    {
        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($purchasePaymentHeader->getId())) {
            $lastPurchasePaymentHeader = $this->purchasePaymentHeaderRepository->findRecentBy($year, $month);
            $currentPurchasePaymentHeader = ($lastPurchasePaymentHeader === null) ? $purchasePaymentHeader : $lastPurchasePaymentHeader;
            $purchasePaymentHeader->setCodeNumberToNext($currentPurchasePaymentHeader->getCodeNumber(), $year, $month);
            
            $purchasePaymentHeader->setStaffFirst($staff);
        }
        $purchasePaymentHeader->setStaffLast($staff);
    }
    
    public function finalize(PurchasePaymentHeader $purchasePaymentHeader, array $params = array())
    {
        foreach ($purchasePaymentHeader->getPurchasePaymentDetails() as $purchasePaymentDetail) {
            $purchasePaymentDetail->setPurchasePaymentHeader($purchasePaymentHeader);
        }
        $this->sync($purchasePaymentHeader);
    }
    
    private function sync(PurchasePaymentHeader $purchasePaymentHeader)
    {
        $purchasePaymentHeader->sync();
        
        $oldPurchasePaymentDetails = $this->purchasePaymentDetailRepository->findByPurchasePaymentHeader($purchasePaymentHeader);
        $newPurchasePaymentDetails = $purchasePaymentHeader->getPurchasePaymentDetails()->getValues();
        $purchaseReceiptHeaders = array();
        foreach ($oldPurchasePaymentDetails as $oldPurchasePaymentDetail) {
            $purchaseReceiptHeaders[] = $oldPurchasePaymentDetail->getPurchaseReceiptHeader();
        }
        foreach ($newPurchasePaymentDetails as $newPurchasePaymentDetail) {
            $purchaseReceiptHeaders[] = $newPurchasePaymentDetail->getPurchaseReceiptHeader();
        }
        $purchaseReceiptHeaderIds = array();
        foreach ($purchaseReceiptHeaders as $purchaseReceiptHeader) {
            $purchaseReceiptHeaderId = $purchaseReceiptHeader->getId();
            if (in_array($purchaseReceiptHeaderId, $purchaseReceiptHeaderIds)) {
                continue;
            }
            $purchaseReceiptHeaderIds[] = $purchaseReceiptHeaderId;
            
            $purchasePaymentDetails = $purchaseReceiptHeader->getPurchasePaymentDetails();
            foreach ($oldPurchasePaymentDetails as $oldPurchasePaymentDetail) {
                if (!in_array($oldPurchasePaymentDetail, $newPurchasePaymentDetails) && $oldPurchasePaymentDetail->getPurchaseReceiptHeader()->getId() === $purchaseReceiptHeaderId) {
                    $purchasePaymentDetails->removeElement($oldPurchasePaymentDetail);
                }
            }
            foreach ($newPurchasePaymentDetails as $newPurchasePaymentDetail) {
                if (!in_array($newPurchasePaymentDetail, $oldPurchasePaymentDetails) && $newPurchasePaymentDetail->getPurchaseReceiptHeader()->getId() === $purchaseReceiptHeaderId) {
                    $purchasePaymentDetails->add($newPurchasePaymentDetail);
                }
            }
            $totalPayment = 0.00;
            foreach ($purchasePaymentDetails as $purchasePaymentDetail) {
                $totalPayment += $purchasePaymentDetail->getAmount();
            }
            $purchaseReceiptHeader->setTotalPayment($totalPayment);
            $purchaseReceiptHeader->setRemaining($purchaseReceiptHeader->getGrandTotal() - $totalPayment);
        }
    }
    
    public function save(PurchasePaymentHeader $purchasePaymentHeader)
    {
        if (empty($purchasePaymentHeader->getId())) {
            ObjectPersister::save(function() use ($purchasePaymentHeader) {
                $this->purchasePaymentHeaderRepository->add($purchasePaymentHeader, array(
                    'purchasePaymentDetails' => array('add' => true),
                ));
                $this->markJournalLedgers($purchasePaymentHeader, true);
            });
        } else {
            ObjectPersister::save(function() use ($purchasePaymentHeader) {
                $this->purchasePaymentHeaderRepository->update($purchasePaymentHeader, array(
                    'purchasePaymentDetails' => array('add' => true, 'remove' => true),
                ));
                $this->markJournalLedgers($purchasePaymentHeader, true);
            });
        }
    }
    
    public function delete(PurchasePaymentHeader $purchasePaymentHeader)
    {
        $this->beforeDelete($purchasePaymentHeader);
        if (!empty($purchasePaymentHeader->getId())) {
            ObjectPersister::save(function() use ($purchasePaymentHeader) {
                $this->purchasePaymentHeaderRepository->remove($purchasePaymentHeader, array(
                    'purchasePaymentDetails' => array('remove' => true),
                ));
                $this->markJournalLedgers($purchasePaymentHeader, true);
            });
        }
    }
    
    public function isValidForDelete(PurchasePaymentHeader $purchasePaymentHeader)
    {
        $valid = $purchasePaymentHeader->getPurchasePaymentDetails()->isEmpty();
        
        return $valid;
    }
    
    protected function beforeDelete(PurchasePaymentHeader $purchasePaymentHeader)
    {
        $purchasePaymentHeader->getPurchasePaymentDetails()->clear();
        $this->sync($purchasePaymentHeader);
    }
    
    private function markJournalLedgers(PurchasePaymentHeader $purchasePaymentHeader, $addForHeader)
    {
        $oldJournalLedgers = $this->journalLedgerRepository->findBy(array(
            'transactionType' => JournalLedger::TRANSACTION_TYPE_PAYABLE_PAYMENT,
            'codeNumberYear' => $purchasePaymentHeader->getCodeNumberYear(),
            'codeNumberMonth' => $purchasePaymentHeader->getCodeNumberMonth(),
            'codeNumberOrdinal' => $purchasePaymentHeader->getCodeNumberOrdinal(),
        ));
        $this->journalLedgerRepository->remove($oldJournalLedgers);
        foreach ($purchasePaymentHeader->getPurchasePaymentDetails() as $purchasePaymentDetail) {
            if ($purchasePaymentDetail->getAmount() > 0.00) {
                $journalLedgerCredit = new JournalLedger();
                $journalLedgerCredit->setCodeNumber($purchasePaymentHeader->getCodeNumber());
                $journalLedgerCredit->setTransactionDate($purchasePaymentHeader->getTransactionDate());
                $journalLedgerCredit->setTransactionType(JournalLedger::TRANSACTION_TYPE_PAYABLE_PAYMENT);
                $journalLedgerCredit->setTransactionSubject($purchasePaymentHeader->getSupplier());
                $journalLedgerCredit->setNote($purchasePaymentHeader->getNote());
                $journalLedgerCredit->setDebit(0.00);
                $journalLedgerCredit->setCredit($purchasePaymentDetail->getAmount());
                $journalLedgerCredit->setAccount($purchasePaymentDetail->getAccount());
                $journalLedgerCredit->setStaff($purchasePaymentHeader->getStaffFirst());
                $this->journalLedgerRepository->add($journalLedgerCredit);

                $journalLedgerDebit = new JournalLedger();
                $journalLedgerDebit->setCodeNumber($purchasePaymentHeader->getCodeNumber());
                $journalLedgerDebit->setTransactionDate($purchasePaymentHeader->getTransactionDate());
                $journalLedgerDebit->setTransactionType(JournalLedger::TRANSACTION_TYPE_PAYABLE_PAYMENT);
                $journalLedgerDebit->setTransactionSubject($purchasePaymentHeader->getSupplier());
                $journalLedgerDebit->setNote($purchasePaymentHeader->getNote());
                $journalLedgerDebit->setDebit($purchasePaymentDetail->getAmount());
                $journalLedgerDebit->setCredit(0.00);
                $journalLedgerDebit->setAccount($purchasePaymentHeader->getSupplier()->getAccountPayable());
                $journalLedgerDebit->setStaff($purchasePaymentHeader->getStaffFirst());
                $this->journalLedgerRepository->add($journalLedgerDebit);
            }
        }
    }
}