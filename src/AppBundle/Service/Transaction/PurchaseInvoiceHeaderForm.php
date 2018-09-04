<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\PurchaseInvoiceHeader;
use AppBundle\Entity\Report\JournalLedger;
use AppBundle\Repository\Transaction\PurchaseInvoiceHeaderRepository;
use AppBundle\Repository\Report\JournalLedgerRepository;
use AppBundle\Repository\Master\AccountRepository;

class PurchaseInvoiceHeaderForm
{
    private $purchaseInvoiceHeaderRepository;
    private $journalLedgerRepository;
    private $accountRepository;
    
    public function __construct(PurchaseInvoiceHeaderRepository $purchaseInvoiceHeaderRepository, JournalLedgerRepository $journalLedgerRepository, AccountRepository $accountRepository)
    {
        $this->purchaseInvoiceHeaderRepository = $purchaseInvoiceHeaderRepository;
        $this->journalLedgerRepository = $journalLedgerRepository;
        $this->accountRepository = $accountRepository;
    }
    
    public function initialize(PurchaseInvoiceHeader $purchaseInvoiceHeader, array $params = array())
    {
        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($purchaseInvoiceHeader->getId())) {
            $lastPurchaseInvoiceHeader = $this->purchaseInvoiceHeaderRepository->findRecentBy($year, $month);
            $currentPurchaseInvoiceHeader = ($lastPurchaseInvoiceHeader === null) ? $purchaseInvoiceHeader : $lastPurchaseInvoiceHeader;
            $purchaseInvoiceHeader->setCodeNumberToNext($currentPurchaseInvoiceHeader->getCodeNumber(), $year, $month);
            
            $purchaseInvoiceHeader->setStaffFirst($staff);
        }
        $purchaseInvoiceHeader->setStaffLast($staff);
    }
    
    public function finalize(PurchaseInvoiceHeader $purchaseInvoiceHeader, array $params = array())
    {
        foreach ($purchaseInvoiceHeader->getPurchaseInvoiceDetails() as $purchaseInvoiceDetail) {
            $purchaseInvoiceDetail->setPurchaseInvoiceHeader($purchaseInvoiceHeader);
        }
        $this->sync($purchaseInvoiceHeader);
    }
    
    private function sync(PurchaseInvoiceHeader $purchaseInvoiceHeader)
    {
        $receiveHeader = $purchaseInvoiceHeader->getReceiveHeader();
        if ($receiveHeader !== null) {
            $receiveDetails = $receiveHeader->getReceiveDetails();
            foreach ($purchaseInvoiceHeader->getPurchaseInvoiceDetails() as $index => $purchaseInvoiceDetail) {
                if ($receiveDetails->containsKey($index)) {
                    $purchaseInvoiceDetail->setReceiveDetail($receiveDetails->get($index));
                }
            }
        }
        foreach ($purchaseInvoiceHeader->getPurchaseInvoiceDetails() as $purchaseInvoiceDetail) {
            $receiveDetail = $purchaseInvoiceDetail->getReceiveDetail();
            $purchaseOrderDetail = $receiveDetail->getPurchaseOrderDetail();
            $purchaseInvoiceDetail->setProduct($purchaseOrderDetail->getProduct());
            $purchaseInvoiceDetail->setQuantity($receiveDetail->getQuantity());
            $purchaseInvoiceDetail->setUnitPrice($purchaseOrderDetail->getUnitPrice());
            $purchaseInvoiceDetail->setDiscount($purchaseOrderDetail->getDiscount());
        }
        $purchaseOrderHeader = $receiveHeader->getPurchaseOrderHeader();
        $purchaseInvoiceHeader->setDiscountPercentage($purchaseOrderHeader->getDiscountPercentage());
        $purchaseInvoiceHeader->setIsTax($purchaseOrderHeader->getIsTax());
        $purchaseInvoiceHeader->setShippingFee($purchaseOrderHeader->getShippingFee());
        $purchaseInvoiceHeader->sync();
    }
    
    public function save(PurchaseInvoiceHeader $purchaseInvoiceHeader)
    {
        if (empty($purchaseInvoiceHeader->getId())) {
            ObjectPersister::save(function() use ($purchaseInvoiceHeader) {
                $this->purchaseInvoiceHeaderRepository->add($purchaseInvoiceHeader, array(
                    'purchaseInvoiceDetails' => array('add' => true),
                ));
                $this->markJournalLedgers($purchaseInvoiceHeader, true);
            });
        } else {
            ObjectPersister::save(function() use ($purchaseInvoiceHeader) {
                $this->purchaseInvoiceHeaderRepository->update($purchaseInvoiceHeader, array(
                    'purchaseInvoiceDetails' => array('add' => true, 'remove' => true),
                ));
                $this->markJournalLedgers($purchaseInvoiceHeader, true);
            });
        }
    }
    
    public function delete(PurchaseInvoiceHeader $purchaseInvoiceHeader)
    {
        $this->beforeDelete($purchaseInvoiceHeader);
        if (!empty($purchaseInvoiceHeader->getId())) {
            ObjectPersister::save(function() use ($purchaseInvoiceHeader) {
                $this->purchaseInvoiceHeaderRepository->remove($purchaseInvoiceHeader, array(
                    'purchaseInvoiceDetails' => array('remove' => true),
                ));
                $this->markJournalLedgers($purchaseInvoiceHeader, true);
            });
        }
    }
    
    protected function beforeDelete(PurchaseInvoiceHeader $purchaseInvoiceHeader)
    {
        $purchaseInvoiceHeader->getPurchaseInvoiceDetails()->clear();
        $this->sync($purchaseInvoiceHeader);
    }
    
    private function markJournalLedgers(PurchaseInvoiceHeader $purchaseInvoiceHeader, $addForHeader)
    {
        $oldJournalLedgers = $this->journalLedgerRepository->findBy(array(
            'transactionType' => JournalLedger::TRANSACTION_TYPE_PAYABLE,
            'codeNumberYear' => $purchaseInvoiceHeader->getCodeNumberYear(),
            'codeNumberMonth' => $purchaseInvoiceHeader->getCodeNumberMonth(),
            'codeNumberOrdinal' => $purchaseInvoiceHeader->getCodeNumberOrdinal(),
        ));
        $this->journalLedgerRepository->remove($oldJournalLedgers);
        if ($purchaseInvoiceHeader->getGrandTotal() > 0.00) {
            $accountInventory = $this->accountRepository->findInventoryRecord();
            
            $journalLedgerCredit = new JournalLedger();
            $journalLedgerCredit->setCodeNumber($purchaseInvoiceHeader->getCodeNumber());
            $journalLedgerCredit->setTransactionDate($purchaseInvoiceHeader->getTransactionDate());
            $journalLedgerCredit->setTransactionType(JournalLedger::TRANSACTION_TYPE_PAYABLE);
            $journalLedgerCredit->setTransactionSubject($purchaseInvoiceHeader->getReceiveHeader()->getPurchaseOrderHeader()->getSupplier());
            $journalLedgerCredit->setNote($purchaseInvoiceHeader->getNote());
            $journalLedgerCredit->setDebit(0.00);
            $journalLedgerCredit->setCredit($purchaseInvoiceHeader->getGrandTotal());
            $journalLedgerCredit->setAccount($purchaseInvoiceHeader->getReceiveHeader()->getPurchaseOrderHeader()->getSupplier()->getAccountPayable());
            $journalLedgerCredit->setStaff($purchaseInvoiceHeader->getStaffFirst());
            $this->journalLedgerRepository->add($journalLedgerCredit);

            $journalLedgerDebit = new JournalLedger();
            $journalLedgerDebit->setCodeNumber($purchaseInvoiceHeader->getCodeNumber());
            $journalLedgerDebit->setTransactionDate($purchaseInvoiceHeader->getTransactionDate());
            $journalLedgerDebit->setTransactionType(JournalLedger::TRANSACTION_TYPE_PAYABLE);
            $journalLedgerDebit->setTransactionSubject($purchaseInvoiceHeader->getReceiveHeader()->getPurchaseOrderHeader()->getSupplier());
            $journalLedgerDebit->setNote($purchaseInvoiceHeader->getNote());
            $journalLedgerDebit->setDebit($purchaseInvoiceHeader->getGrandTotal());
            $journalLedgerDebit->setCredit(0.00);
            $journalLedgerDebit->setAccount($accountInventory);
            $journalLedgerDebit->setStaff($purchaseInvoiceHeader->getStaffFirst());
            $this->journalLedgerRepository->add($journalLedgerDebit);
        }
    }
}