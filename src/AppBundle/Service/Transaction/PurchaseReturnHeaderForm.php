<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\PurchaseReturnHeader;
use AppBundle\Entity\Report\Inventory;
use AppBundle\Entity\Report\JournalLedger;
use AppBundle\Repository\Transaction\PurchaseReturnHeaderRepository;
use AppBundle\Repository\Report\InventoryRepository;
use AppBundle\Repository\Report\JournalLedgerRepository;
use AppBundle\Repository\Master\AccountRepository;

class PurchaseReturnHeaderForm
{
    private $purchaseReturnHeaderRepository;
    private $inventoryRepository;
    private $journalLedgerRepository;
    private $accountRepository;
    
    public function __construct(PurchaseReturnHeaderRepository $purchaseReturnHeaderRepository, InventoryRepository $inventoryRepository, JournalLedgerRepository $journalLedgerRepository, AccountRepository $accountRepository)
    {
        $this->purchaseReturnHeaderRepository = $purchaseReturnHeaderRepository;
        $this->inventoryRepository = $inventoryRepository;
        $this->journalLedgerRepository = $journalLedgerRepository;
        $this->accountRepository = $accountRepository;
    }
    
    public function initialize(PurchaseReturnHeader $purchaseReturnHeader, array $params = array())
    {
        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($purchaseReturnHeader->getId())) {
            $lastPurchaseReturnHeader = $this->purchaseReturnHeaderRepository->findRecentBy($year, $month);
            $currentPurchaseReturnHeader = ($lastPurchaseReturnHeader === null) ? $purchaseReturnHeader : $lastPurchaseReturnHeader;
            $purchaseReturnHeader->setCodeNumberToNext($currentPurchaseReturnHeader->getCodeNumber(), $year, $month);
            
            $purchaseReturnHeader->setStaffFirst($staff);
        }
        $purchaseReturnHeader->setStaffLast($staff);
    }
    
    public function finalize(PurchaseReturnHeader $purchaseReturnHeader, array $params = array())
    {
        foreach ($purchaseReturnHeader->getPurchaseReturnDetails() as $purchaseReturnDetail) {
            $purchaseReturnDetail->setPurchaseReturnHeader($purchaseReturnHeader);
        }
        $this->sync($purchaseReturnHeader);
    }
    
    private function sync(PurchaseReturnHeader $purchaseReturnHeader)
    {
        $purchaseInvoiceHeader = $purchaseReturnHeader->getPurchaseInvoiceHeader();
        if ($purchaseInvoiceHeader !== null) {
            $purchaseInvoiceDetails = $purchaseInvoiceHeader->getPurchaseInvoiceDetails();
            foreach ($purchaseReturnHeader->getPurchaseReturnDetails() as $index => $purchaseReturnDetail) {
                if ($purchaseInvoiceDetails->containsKey($index)) {
                    $purchaseReturnDetail->setPurchaseInvoiceDetail($purchaseInvoiceDetails->get($index));
                }
            }
        }
        foreach ($purchaseReturnHeader->getPurchaseReturnDetails() as $purchaseReturnDetail) {
            $purchaseReturnDetail->setUnitPrice($purchaseReturnDetail->getPurchaseInvoiceDetail()->getUnitPrice());
        }
        $purchaseReturnHeader->sync();
    }
    
    public function save(PurchaseReturnHeader $purchaseReturnHeader)
    {
        if (empty($purchaseReturnHeader->getId())) {
            ObjectPersister::save(function() use ($purchaseReturnHeader) {
                $this->purchaseReturnHeaderRepository->add($purchaseReturnHeader, array(
                    'purchaseReturnDetails' => array('add' => true),
                ));
                $this->markInventories($purchaseReturnHeader);
                $this->markJournalLedgers($purchaseReturnHeader);
            });
        } else {
            ObjectPersister::save(function() use ($purchaseReturnHeader) {
                $this->purchaseReturnHeaderRepository->update($purchaseReturnHeader, array(
                    'purchaseReturnDetails' => array('add' => true, 'remove' => true),
                ));
                $this->markInventories($purchaseReturnHeader);
                $this->markJournalLedgers($purchaseReturnHeader);
            });
        }
    }
    
    public function delete(PurchaseReturnHeader $purchaseReturnHeader)
    {
        $this->beforeDelete($purchaseReturnHeader);
        if (!empty($purchaseReturnHeader->getId())) {
            ObjectPersister::save(function() use ($purchaseReturnHeader) {
                $this->purchaseReturnHeaderRepository->remove($purchaseReturnHeader, array(
                    'purchaseReturnDetails' => array('remove' => true),
                ));
                $this->markInventories($purchaseReturnHeader);
                $this->markJournalLedgers($purchaseReturnHeader);
            });
        }
    }
    
    protected function beforeDelete(PurchaseReturnHeader $purchaseReturnHeader)
    {
        $purchaseReturnHeader->getPurchaseReturnDetails()->clear();
        $this->sync($purchaseReturnHeader);
    }
    
    private function markInventories(PurchaseReturnHeader $purchaseReturnHeader)
    {
        $oldInventories = $this->inventoryRepository->findBy(array(
            'transactionType' => Inventory::TRANSACTION_TYPE_PURCHASE_RETURN,
            'codeNumberYear' => $purchaseReturnHeader->getCodeNumberYear(),
            'codeNumberMonth' => $purchaseReturnHeader->getCodeNumberMonth(),
            'codeNumberOrdinal' => $purchaseReturnHeader->getCodeNumberOrdinal(),
        ));
        $this->inventoryRepository->remove($oldInventories);
        foreach ($purchaseReturnHeader->getPurchaseReturnDetails() as $purchaseReturnDetail) {
            if ($purchaseReturnDetail->getQuantity() > 0) {
                $inventory = new Inventory();
                $inventory->setCodeNumber($purchaseReturnHeader->getCodeNumber());
                $inventory->setTransactionDate($purchaseReturnHeader->getTransactionDate());
                $inventory->setTransactionType(Inventory::TRANSACTION_TYPE_PURCHASE_RETURN);
                $inventory->setTransactionSubject($purchaseReturnHeader->getPurchaseInvoiceHeader()->getReceiveHeader()->getPurchaseOrderHeader()->getSupplier());
                $inventory->setNote($purchaseReturnHeader->getNote());
                $inventory->setQuantityIn(0);
                $inventory->setQuantityOut($purchaseReturnDetail->getQuantity());
                $inventory->setUnitPrice($purchaseReturnDetail->getUnitPrice());
                $inventory->setProduct($purchaseReturnDetail->getPurchaseInvoiceDetail()->getReceiveDetail()->getPurchaseOrderDetail()->getProduct());
                $inventory->setWarehouse($purchaseReturnHeader->getWarehouse());
                $inventory->setStaff($purchaseReturnHeader->getStaffFirst());
                $this->inventoryRepository->add($inventory);
            }
        }
    }
    
    private function markJournalLedgers(PurchaseReturnHeader $purchaseReturnHeader)
    {
        $oldJournalLedgers = $this->journalLedgerRepository->findBy(array(
            'transactionType' => JournalLedger::TRANSACTION_TYPE_PURCHASE_RETURN,
            'codeNumberYear' => $purchaseReturnHeader->getCodeNumberYear(),
            'codeNumberMonth' => $purchaseReturnHeader->getCodeNumberMonth(),
            'codeNumberOrdinal' => $purchaseReturnHeader->getCodeNumberOrdinal(),
        ));
        $this->journalLedgerRepository->remove($oldJournalLedgers);
        if ($purchaseReturnHeader->getGrandTotal() > 0.00) {
            $accountInventory = $this->accountRepository->findInventoryRecord();
            
            $journalLedgerCredit = new JournalLedger();
            $journalLedgerCredit->setCodeNumber($purchaseReturnHeader->getCodeNumber());
            $journalLedgerCredit->setTransactionDate($purchaseReturnHeader->getTransactionDate());
            $journalLedgerCredit->setTransactionType(JournalLedger::TRANSACTION_TYPE_PURCHASE_RETURN);
            $journalLedgerCredit->setTransactionSubject($purchaseReturnHeader->getPurchaseInvoiceHeader()->getReceiveHeader()->getPurchaseOrderHeader()->getSupplier());
            $journalLedgerCredit->setNote($purchaseReturnHeader->getNote());
            $journalLedgerCredit->setDebit(0.00);
            $journalLedgerCredit->setCredit($purchaseReturnHeader->getGrandTotal());
            $journalLedgerCredit->setAccount($accountInventory);
            $journalLedgerCredit->setStaff($purchaseReturnHeader->getStaffFirst());
            $this->journalLedgerRepository->add($journalLedgerCredit);

            $journalLedgerDebit = new JournalLedger();
            $journalLedgerDebit->setCodeNumber($purchaseReturnHeader->getCodeNumber());
            $journalLedgerDebit->setTransactionDate($purchaseReturnHeader->getTransactionDate());
            $journalLedgerDebit->setTransactionType(JournalLedger::TRANSACTION_TYPE_PURCHASE_RETURN);
            $journalLedgerDebit->setTransactionSubject($purchaseReturnHeader->getPurchaseInvoiceHeader()->getReceiveHeader()->getPurchaseOrderHeader()->getSupplier());
            $journalLedgerDebit->setNote($purchaseReturnHeader->getNote());
            $journalLedgerDebit->setDebit($purchaseReturnHeader->getGrandTotal());
            $journalLedgerDebit->setCredit(0.00);
            $journalLedgerDebit->setAccount($purchaseReturnHeader->getPurchaseInvoiceHeader()->getReceiveHeader()->getPurchaseOrderHeader()->getSupplier()->getAccountPayable());
            $journalLedgerDebit->setStaff($purchaseReturnHeader->getStaffFirst());
            $this->journalLedgerRepository->add($journalLedgerDebit);
        }
    }
}