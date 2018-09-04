<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\SaleInvoiceHeader;
use AppBundle\Entity\Report\Inventory;
use AppBundle\Entity\Report\JournalLedger;
use AppBundle\Repository\Transaction\SaleInvoiceHeaderRepository;
use AppBundle\Repository\Transaction\PurchaseInvoiceDetailRepository;
use AppBundle\Repository\Report\InventoryRepository;
use AppBundle\Repository\Report\JournalLedgerRepository;
use AppBundle\Repository\Master\AccountRepository;

class SaleInvoiceHeaderForm
{
    private $saleInvoiceHeaderRepository;
    private $purchaseInvoiceDetailRepository;
    private $inventoryRepository;
    private $journalLedgerRepository;
    private $accountRepository;
    
    public function __construct(SaleInvoiceHeaderRepository $saleInvoiceHeaderRepository, PurchaseInvoiceDetailRepository $purchaseInvoiceDetailRepository, InventoryRepository $inventoryRepository, JournalLedgerRepository $journalLedgerRepository, AccountRepository $accountRepository)
    {
        $this->saleInvoiceHeaderRepository = $saleInvoiceHeaderRepository;
        $this->purchaseInvoiceDetailRepository = $purchaseInvoiceDetailRepository;
        $this->inventoryRepository = $inventoryRepository;
        $this->journalLedgerRepository = $journalLedgerRepository;
        $this->accountRepository = $accountRepository;
    }
    
    public function initialize(SaleInvoiceHeader $saleInvoiceHeader, array $params = array())
    {
        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($saleInvoiceHeader->getId())) {
            $lastSaleInvoiceHeader = $this->saleInvoiceHeaderRepository->findRecentBy($year, $month);
            $currentSaleInvoiceHeader = ($lastSaleInvoiceHeader === null) ? $saleInvoiceHeader : $lastSaleInvoiceHeader;
            $saleInvoiceHeader->setCodeNumberToNext($currentSaleInvoiceHeader->getCodeNumber(), $year, $month);
            
            $saleInvoiceHeader->setStaffFirst($staff);
        }
        $saleInvoiceHeader->setStaffLast($staff);
    }
    
    public function finalize(SaleInvoiceHeader $saleInvoiceHeader, array $params = array())
    {
        foreach ($saleInvoiceHeader->getSaleInvoiceDetails() as $saleInvoiceDetail) {
            $saleInvoiceDetail->setSaleInvoiceHeader($saleInvoiceHeader);
            $averagePurchasePrice = $this->purchaseInvoiceDetailRepository->getAveragePurchasePriceByProduct($saleInvoiceDetail->getProduct());
            $saleInvoiceDetail->setAveragePurchasePrice($averagePurchasePrice);
        }
        $this->sync($saleInvoiceHeader);
    }
    
    private function sync(SaleInvoiceHeader $saleInvoiceHeader)
    {
        $saleInvoiceHeader->sync();
    }
    
    public function save(SaleInvoiceHeader $saleInvoiceHeader)
    {
        if (empty($saleInvoiceHeader->getId())) {
            ObjectPersister::save(function() use ($saleInvoiceHeader) {
                $this->saleInvoiceHeaderRepository->add($saleInvoiceHeader, array(
                    'saleInvoiceDetails' => array('add' => true),
                ));
                $this->markInventories($saleInvoiceHeader);
                $this->markJournalLedgers($saleInvoiceHeader);
            });
        } else {
            ObjectPersister::save(function() use ($saleInvoiceHeader) {
                $this->saleInvoiceHeaderRepository->update($saleInvoiceHeader, array(
                    'saleInvoiceDetails' => array('add' => true, 'remove' => true),
                ));
                $this->markInventories($saleInvoiceHeader);
                $this->markJournalLedgers($saleInvoiceHeader);
            });
        }
    }
    
    public function delete(SaleInvoiceHeader $saleInvoiceHeader)
    {
        $this->beforeDelete($saleInvoiceHeader);
        if (!empty($saleInvoiceHeader->getId())) {
            ObjectPersister::save(function() use ($saleInvoiceHeader) {
                $this->saleInvoiceHeaderRepository->remove($saleInvoiceHeader, array(
                    'saleInvoiceDetails' => array('remove' => true),
                ));
                $this->markInventories($saleInvoiceHeader);
                $this->markJournalLedgers($saleInvoiceHeader);
            });
        }
    }
    
    protected function beforeDelete(SaleInvoiceHeader $saleInvoiceHeader)
    {
        $saleInvoiceHeader->getSaleInvoiceDetails()->clear();
        $this->sync($saleInvoiceHeader);
    }
    
    private function markInventories(SaleInvoiceHeader $saleInvoiceHeader)
    {
        $oldInventories = $this->inventoryRepository->findBy(array(
            'transactionType' => Inventory::TRANSACTION_TYPE_DELIVERY,
            'codeNumberYear' => $saleInvoiceHeader->getCodeNumberYear(),
            'codeNumberMonth' => $saleInvoiceHeader->getCodeNumberMonth(),
            'codeNumberOrdinal' => $saleInvoiceHeader->getCodeNumberOrdinal(),
        ));
        $this->inventoryRepository->remove($oldInventories);
        foreach ($saleInvoiceHeader->getSaleInvoiceDetails() as $saleInvoiceDetail) {
            if ($saleInvoiceDetail->getQuantity() > 0) {
                $inventory = new Inventory();
                $inventory->setCodeNumber($saleInvoiceHeader->getCodeNumber());
                $inventory->setTransactionDate($saleInvoiceHeader->getTransactionDate());
                $inventory->setTransactionType(Inventory::TRANSACTION_TYPE_DELIVERY);
                $inventory->setTransactionSubject($saleInvoiceHeader->getCustomer());
                $inventory->setNote($saleInvoiceHeader->getNote());
                $inventory->setQuantityIn(0);
                $inventory->setQuantityOut($saleInvoiceDetail->getQuantity());
                $inventory->setUnitPrice($saleInvoiceDetail->getUnitPrice());
                $inventory->setProduct($saleInvoiceDetail->getProduct());
                $inventory->setWarehouse($saleInvoiceHeader->getWarehouse());
                $inventory->setStaff($saleInvoiceHeader->getStaffFirst());
                $this->inventoryRepository->add($inventory);
            }
        }
    }
    
    private function markJournalLedgers(SaleInvoiceHeader $saleInvoiceHeader)
    {
        $oldJournalLedgers = $this->journalLedgerRepository->findBy(array(
            'transactionType' => JournalLedger::TRANSACTION_TYPE_RECEIVABLE,
            'codeNumberYear' => $saleInvoiceHeader->getCodeNumberYear(),
            'codeNumberMonth' => $saleInvoiceHeader->getCodeNumberMonth(),
            'codeNumberOrdinal' => $saleInvoiceHeader->getCodeNumberOrdinal(),
        ));
        $this->journalLedgerRepository->remove($oldJournalLedgers);
        if ($saleInvoiceHeader->getGrandTotal() > 0.00) {
            $accountSale = $this->accountRepository->findSaleRecord();
            $accountInventory = $this->accountRepository->findInventoryRecord();
            $accountCogsOut = $this->accountRepository->findCogsOutRecord();
            
            $journalLedgerSalesCredit = new JournalLedger();
            $journalLedgerSalesCredit->setCodeNumber($saleInvoiceHeader->getCodeNumber());
            $journalLedgerSalesCredit->setTransactionDate($saleInvoiceHeader->getTransactionDate());
            $journalLedgerSalesCredit->setTransactionType(JournalLedger::TRANSACTION_TYPE_RECEIVABLE);
            $journalLedgerSalesCredit->setTransactionSubject($saleInvoiceHeader->getCustomer());
            $journalLedgerSalesCredit->setNote($saleInvoiceHeader->getNote());
            $journalLedgerSalesCredit->setDebit(0.00);
            $journalLedgerSalesCredit->setCredit($saleInvoiceHeader->getGrandTotal());
            $journalLedgerSalesCredit->setAccount($accountSale);
            $journalLedgerSalesCredit->setStaff($saleInvoiceHeader->getStaffFirst());
            $this->journalLedgerRepository->add($journalLedgerSalesCredit);

            $journalLedgerReceivableDebit = new JournalLedger();
            $journalLedgerReceivableDebit->setCodeNumber($saleInvoiceHeader->getCodeNumber());
            $journalLedgerReceivableDebit->setTransactionDate($saleInvoiceHeader->getTransactionDate());
            $journalLedgerReceivableDebit->setTransactionType(JournalLedger::TRANSACTION_TYPE_RECEIVABLE);
            $journalLedgerReceivableDebit->setTransactionSubject($saleInvoiceHeader->getCustomer());
            $journalLedgerReceivableDebit->setNote($saleInvoiceHeader->getNote());
            $journalLedgerReceivableDebit->setDebit($saleInvoiceHeader->getGrandTotal());
            $journalLedgerReceivableDebit->setCredit(0.00);
            $journalLedgerReceivableDebit->setAccount($saleInvoiceHeader->getCustomer()->getAccountReceivable());
            $journalLedgerReceivableDebit->setStaff($saleInvoiceHeader->getStaffFirst());
            $this->journalLedgerRepository->add($journalLedgerReceivableDebit);
            
            $journalLedgerInventoryCredit = new JournalLedger();
            $journalLedgerInventoryCredit->setCodeNumber($saleInvoiceHeader->getCodeNumber());
            $journalLedgerInventoryCredit->setTransactionDate($saleInvoiceHeader->getTransactionDate());
            $journalLedgerInventoryCredit->setTransactionType(JournalLedger::TRANSACTION_TYPE_RECEIVABLE);
            $journalLedgerInventoryCredit->setTransactionSubject($saleInvoiceHeader->getCustomer());
            $journalLedgerInventoryCredit->setNote($saleInvoiceHeader->getNote());
            $journalLedgerInventoryCredit->setDebit(0.00);
            $journalLedgerInventoryCredit->setCredit($saleInvoiceHeader->getTotalAveragePurchasePrice());
            $journalLedgerInventoryCredit->setAccount($accountInventory);
            $journalLedgerInventoryCredit->setStaff($saleInvoiceHeader->getStaffFirst());
            $this->journalLedgerRepository->add($journalLedgerInventoryCredit);

            $journalLedgerCogsDebit = new JournalLedger();
            $journalLedgerCogsDebit->setCodeNumber($saleInvoiceHeader->getCodeNumber());
            $journalLedgerCogsDebit->setTransactionDate($saleInvoiceHeader->getTransactionDate());
            $journalLedgerCogsDebit->setTransactionType(JournalLedger::TRANSACTION_TYPE_RECEIVABLE);
            $journalLedgerCogsDebit->setTransactionSubject($saleInvoiceHeader->getCustomer());
            $journalLedgerCogsDebit->setNote($saleInvoiceHeader->getNote());
            $journalLedgerCogsDebit->setDebit($saleInvoiceHeader->getTotalAveragePurchasePrice());
            $journalLedgerCogsDebit->setCredit(0.00);
            $journalLedgerCogsDebit->setAccount($accountCogsOut);
            $journalLedgerCogsDebit->setStaff($saleInvoiceHeader->getStaffFirst());
            $this->journalLedgerRepository->add($journalLedgerCogsDebit);
        }
    }
}