<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\SaleReturnHeader;
use AppBundle\Entity\Report\Inventory;
use AppBundle\Entity\Report\JournalLedger;
use AppBundle\Repository\Transaction\SaleReturnHeaderRepository;
use AppBundle\Repository\Report\InventoryRepository;
use AppBundle\Repository\Report\JournalLedgerRepository;
use AppBundle\Repository\Master\AccountRepository;

class SaleReturnHeaderForm
{
    private $saleReturnHeaderRepository;
    private $inventoryRepository;
    private $journalLedgerRepository;
    private $accountRepository;
    
    public function __construct(SaleReturnHeaderRepository $saleReturnHeaderRepository, InventoryRepository $inventoryRepository, JournalLedgerRepository $journalLedgerRepository, AccountRepository $accountRepository)
    {
        $this->saleReturnHeaderRepository = $saleReturnHeaderRepository;
        $this->inventoryRepository = $inventoryRepository;
        $this->journalLedgerRepository = $journalLedgerRepository;
        $this->accountRepository = $accountRepository;
    }
    
    public function initialize(SaleReturnHeader $saleReturnHeader, array $params = array())
    {
        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($saleReturnHeader->getId())) {
            $lastSaleReturnHeader = $this->saleReturnHeaderRepository->findRecentBy($year, $month);
            $currentSaleReturnHeader = ($lastSaleReturnHeader === null) ? $saleReturnHeader : $lastSaleReturnHeader;
            $saleReturnHeader->setCodeNumberToNext($currentSaleReturnHeader->getCodeNumber(), $year, $month);
            
            $saleReturnHeader->setStaffFirst($staff);
        }
        $saleReturnHeader->setStaffLast($staff);
    }
    
    public function finalize(SaleReturnHeader $saleReturnHeader, array $params = array())
    {
        foreach ($saleReturnHeader->getSaleReturnDetails() as $saleReturnDetail) {
            $saleReturnDetail->setSaleReturnHeader($saleReturnHeader);
        }
        $this->sync($saleReturnHeader);
    }
    
    private function sync(SaleReturnHeader $saleReturnHeader)
    {
        $saleInvoiceHeader = $saleReturnHeader->getSaleInvoiceHeader();
        if ($saleInvoiceHeader !== null) {
            $saleInvoiceDetails = $saleInvoiceHeader->getSaleInvoiceDetails();
            foreach ($saleReturnHeader->getSaleReturnDetails() as $index => $saleReturnDetail) {
                if ($saleInvoiceDetails->containsKey($index)) {
                    $saleReturnDetail->setSaleInvoiceDetail($saleInvoiceDetails->get($index));
                }
            }
        }
        foreach ($saleReturnHeader->getSaleReturnDetails() as $saleReturnDetail) {
            $saleInvoiceDetail = $saleReturnDetail->getSaleInvoiceDetail();
            $saleReturnDetail->setUnitPrice($saleInvoiceDetail->getUnitPrice());
        }
        $saleReturnHeader->sync();
    }
    
    public function save(SaleReturnHeader $saleReturnHeader)
    {
        if (empty($saleReturnHeader->getId())) {
            ObjectPersister::save(function() use ($saleReturnHeader) {
                $this->saleReturnHeaderRepository->add($saleReturnHeader, array(
                    'saleReturnDetails' => array('add' => true),
                ));
                $this->markInventories($saleReturnHeader, true);
                $this->markJournalLedgers($saleReturnHeader, true);
            });
        } else {
            ObjectPersister::save(function() use ($saleReturnHeader) {
                $this->saleReturnHeaderRepository->update($saleReturnHeader, array(
                    'saleReturnDetails' => array('add' => true, 'remove' => true),
                ));
                $this->markInventories($saleReturnHeader, true);
                $this->markJournalLedgers($saleReturnHeader, true);
            });
        }
    }
    
    public function delete(SaleReturnHeader $saleReturnHeader)
    {
        $this->beforeDelete($saleReturnHeader);
        if (!empty($saleReturnHeader->getId())) {
            ObjectPersister::save(function() use ($saleReturnHeader) {
                $this->saleReturnHeaderRepository->remove($saleReturnHeader, array(
                    'saleReturnDetails' => array('remove' => true),
                ));
                $this->markInventories($saleReturnHeader, true);
                $this->markJournalLedgers($saleReturnHeader, true);
            });
        }
    }
    
    protected function beforeDelete(SaleReturnHeader $saleReturnHeader)
    {
        $saleReturnHeader->getSaleReturnDetails()->clear();
        $this->sync($saleReturnHeader);
    }
    
    private function markInventories(SaleReturnHeader $saleReturnHeader, $addForHeader)
    {
        $oldInventories = $this->inventoryRepository->findBy(array(
            'transactionType' => Inventory::TRANSACTION_TYPE_SALE_RETURN,
            'codeNumberYear' => $saleReturnHeader->getCodeNumberYear(),
            'codeNumberMonth' => $saleReturnHeader->getCodeNumberMonth(),
            'codeNumberOrdinal' => $saleReturnHeader->getCodeNumberOrdinal(),
        ));
        $this->inventoryRepository->remove($oldInventories);
        foreach ($saleReturnHeader->getSaleReturnDetails() as $saleReturnDetail) {
            if ($saleReturnDetail->getQuantity() > 0) {
                $inventory = new Inventory();
                $inventory->setCodeNumber($saleReturnHeader->getCodeNumber());
                $inventory->setTransactionDate($saleReturnHeader->getTransactionDate());
                $inventory->setTransactionType(Inventory::TRANSACTION_TYPE_SALE_RETURN);
                $inventory->setTransactionSubject($saleReturnHeader->getSaleInvoiceHeader()->getCustomer());
                $inventory->setNote($saleReturnHeader->getNote());
                $inventory->setQuantityIn($saleReturnDetail->getQuantity());
                $inventory->setQuantityOut(0);
                $inventory->setUnitPrice($saleReturnDetail->getUnitPrice());
                $inventory->setProduct($saleReturnDetail->getSaleInvoiceDetail()->getProduct());
                $inventory->setWarehouse($saleReturnHeader->getWarehouse());
                $inventory->setStaff($saleReturnHeader->getStaffFirst());
                $this->inventoryRepository->add($inventory);
            }
        }
    }
    
    private function markJournalLedgers(SaleReturnHeader $saleReturnHeader, $addForHeader)
    {
        $oldJournalLedgers = $this->journalLedgerRepository->findBy(array(
            'transactionType' => JournalLedger::TRANSACTION_TYPE_SALE_RETURN,
            'codeNumberYear' => $saleReturnHeader->getCodeNumberYear(),
            'codeNumberMonth' => $saleReturnHeader->getCodeNumberMonth(),
            'codeNumberOrdinal' => $saleReturnHeader->getCodeNumberOrdinal(),
        ));
        $this->journalLedgerRepository->remove($oldJournalLedgers);
        if ($saleReturnHeader->getGrandTotal() > 0.00) {
            $accountSaleReturn = $this->accountRepository->findSaleReturnRecord();
            $accountInventory = $this->accountRepository->findInventoryRecord();
            $accountCogsIn = $this->accountRepository->findCogsInRecord();
            
            $journalLedgerReceivableCredit = new JournalLedger();
            $journalLedgerReceivableCredit->setCodeNumber($saleReturnHeader->getCodeNumber());
            $journalLedgerReceivableCredit->setTransactionDate($saleReturnHeader->getTransactionDate());
            $journalLedgerReceivableCredit->setTransactionType(JournalLedger::TRANSACTION_TYPE_SALE_RETURN);
            $journalLedgerReceivableCredit->setTransactionSubject($saleReturnHeader->getSaleInvoiceHeader()->getCustomer());
            $journalLedgerReceivableCredit->setNote($saleReturnHeader->getNote());
            $journalLedgerReceivableCredit->setDebit(0.00);
            $journalLedgerReceivableCredit->setCredit($saleReturnHeader->getGrandTotal());
            $journalLedgerReceivableCredit->setAccount($saleReturnHeader->getSaleInvoiceHeader()->getCustomer()->getAccountReceivable());
            $journalLedgerReceivableCredit->setStaff($saleReturnHeader->getStaffFirst());
            $this->journalLedgerRepository->add($journalLedgerReceivableCredit);

            $journalLedgerReturnDebit = new JournalLedger();
            $journalLedgerReturnDebit->setCodeNumber($saleReturnHeader->getCodeNumber());
            $journalLedgerReturnDebit->setTransactionDate($saleReturnHeader->getTransactionDate());
            $journalLedgerReturnDebit->setTransactionType(JournalLedger::TRANSACTION_TYPE_SALE_RETURN);
            $journalLedgerReturnDebit->setTransactionSubject($saleReturnHeader->getSaleInvoiceHeader()->getCustomer());
            $journalLedgerReturnDebit->setNote($saleReturnHeader->getNote());
            $journalLedgerReturnDebit->setDebit($saleReturnHeader->getGrandTotal());
            $journalLedgerReturnDebit->setCredit(0.00);
            $journalLedgerReturnDebit->setAccount($accountSaleReturn);
            $journalLedgerReturnDebit->setStaff($saleReturnHeader->getStaffFirst());
            $this->journalLedgerRepository->add($journalLedgerReturnDebit);
            
            $journalLedgerInventoryDebit = new JournalLedger();
            $journalLedgerInventoryDebit->setCodeNumber($saleReturnHeader->getCodeNumber());
            $journalLedgerInventoryDebit->setTransactionDate($saleReturnHeader->getTransactionDate());
            $journalLedgerInventoryDebit->setTransactionType(JournalLedger::TRANSACTION_TYPE_SALE_RETURN);
            $journalLedgerInventoryDebit->setTransactionSubject($saleReturnHeader->getSaleInvoiceHeader()->getCustomer());
            $journalLedgerInventoryDebit->setNote($saleReturnHeader->getNote());
            $journalLedgerInventoryDebit->setDebit($saleReturnHeader->getSaleInvoiceHeader()->getTotalAveragePurchasePrice());
            $journalLedgerInventoryDebit->setCredit(0.00);
            $journalLedgerInventoryDebit->setAccount($accountInventory);
            $journalLedgerInventoryDebit->setStaff($saleReturnHeader->getStaffFirst());
            $this->journalLedgerRepository->add($journalLedgerInventoryDebit);

            $journalLedgerCogsCredit = new JournalLedger();
            $journalLedgerCogsCredit->setCodeNumber($saleReturnHeader->getCodeNumber());
            $journalLedgerCogsCredit->setTransactionDate($saleReturnHeader->getTransactionDate());
            $journalLedgerCogsCredit->setTransactionType(JournalLedger::TRANSACTION_TYPE_SALE_RETURN);
            $journalLedgerCogsCredit->setTransactionSubject($saleReturnHeader->getSaleInvoiceHeader()->getCustomer());
            $journalLedgerCogsCredit->setNote($saleReturnHeader->getNote());
            $journalLedgerCogsCredit->setDebit(0.00);
            $journalLedgerCogsCredit->setCredit($saleReturnHeader->getSaleInvoiceHeader()->getTotalAveragePurchasePrice());
            $journalLedgerCogsCredit->setAccount($accountCogsIn);
            $journalLedgerCogsCredit->setStaff($saleReturnHeader->getStaffFirst());
            $this->journalLedgerRepository->add($journalLedgerCogsCredit);
        }
    }
}