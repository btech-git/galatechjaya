<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\TransferHeader;
use AppBundle\Entity\Report\Inventory;
use AppBundle\Repository\Transaction\TransferHeaderRepository;
use AppBundle\Repository\Report\InventoryRepository;

class TransferHeaderForm
{
    private $transferHeaderRepository;
    private $inventoryRepository;
    
    public function __construct(TransferHeaderRepository $transferHeaderRepository, InventoryRepository $inventoryRepository)
    {
        $this->transferHeaderRepository = $transferHeaderRepository;
        $this->inventoryRepository = $inventoryRepository;
    }
    
    public function initialize(TransferHeader $transferHeader, array $params = array())
    {
        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($transferHeader->getId())) {
            $lastTransferHeader = $this->transferHeaderRepository->findRecentBy($year, $month);
            $currentTransferHeader = ($lastTransferHeader === null) ? $transferHeader : $lastTransferHeader;
            $transferHeader->setCodeNumberToNext($currentTransferHeader->getCodeNumber(), $year, $month);
            
            $transferHeader->setStaffFirst($staff);
        }
        $transferHeader->setStaffLast($staff);
    }
    
    public function finalize(TransferHeader $transferHeader, array $params = array())
    {
        foreach ($transferHeader->getTransferDetails() as $transferDetail) {
            $transferDetail->setTransferHeader($transferHeader);
        }
        if (empty($transferHeader->getId())) {
            foreach ($transferHeader->getTransferDetails() as $transferDetail) {
                $stock = $this->inventoryRepository->getStockByProductAndWarehouse($transferDetail->getProduct(), $transferHeader->getWarehouseFrom());
                $transferDetail->setQuantityCurrent($stock);
            }
        }
        $this->sync($transferHeader);
    }
    
    private function sync(TransferHeader $transferHeader)
    {
        $transferHeader->sync();
    }
    
    public function save(TransferHeader $transferHeader)
    {
        if (empty($transferHeader->getId())) {
            ObjectPersister::save(function() use ($transferHeader) {
                $this->transferHeaderRepository->add($transferHeader, array(
                    'transferDetails' => array('add' => true),
                ));
                $this->markInventories($transferHeader, true);
            });
        } else {
            ObjectPersister::save(function() use ($transferHeader) {
                $this->transferHeaderRepository->update($transferHeader, array(
                    'transferDetails' => array('add' => true, 'remove' => true),
                ));
                $this->markInventories($transferHeader, true);
            });
        }
    }
    
    public function delete(TransferHeader $transferHeader)
    {
        $this->beforeDelete($transferHeader);
        if (!empty($transferHeader->getId())) {
            ObjectPersister::save(function() use ($transferHeader) {
                $this->transferHeaderRepository->remove($transferHeader, array(
                    'transferDetails' => array('remove' => true),
                ));
                $this->markInventories($transferHeader, true);
            });
        }
    }
    
    protected function beforeDelete(TransferHeader $transferHeader)
    {
        $transferHeader->getTransferDetails()->clear();
        $this->sync($transferHeader);
    }
    
    private function markInventories(TransferHeader $transferHeader, $addForHeader)
    {
        $oldInventories = $this->inventoryRepository->findBy(array(
            'transactionType' => Inventory::TRANSACTION_TYPE_TRANSFER,
            'codeNumberYear' => $transferHeader->getCodeNumberYear(),
            'codeNumberMonth' => $transferHeader->getCodeNumberMonth(),
            'codeNumberOrdinal' => $transferHeader->getCodeNumberOrdinal(),
        ));
        $this->inventoryRepository->remove($oldInventories);
        foreach ($transferHeader->getTransferDetails() as $transferDetail) {
            if ($transferDetail->getQuantity() > 0) {
                $inventoryFrom = new Inventory();
                $inventoryFrom->setCodeNumber($transferHeader->getCodeNumber());
                $inventoryFrom->setTransactionDate($transferHeader->getTransactionDate());
                $inventoryFrom->setTransactionType(Inventory::TRANSACTION_TYPE_TRANSFER);
                $inventoryFrom->setTransactionSubject($transferHeader->getWarehouseFrom());
                $inventoryFrom->setNote($transferHeader->getNote());
                $inventoryFrom->setQuantityIn(0);
                $inventoryFrom->setQuantityOut($transferDetail->getQuantity());
                $inventoryFrom->setUnitPrice(0.00);
                $inventoryFrom->setProduct($transferDetail->getProduct());
                $inventoryFrom->setWarehouse($transferHeader->getWarehouseFrom());
                $inventoryFrom->setStaff($transferHeader->getStaffFirst());
                $this->inventoryRepository->add($inventoryFrom);
                
                $inventoryTo = new Inventory();
                $inventoryTo->setCodeNumber($transferHeader->getCodeNumber());
                $inventoryTo->setTransactionDate($transferHeader->getTransactionDate());
                $inventoryTo->setTransactionType(Inventory::TRANSACTION_TYPE_TRANSFER);
                $inventoryTo->setTransactionSubject($transferHeader->getWarehouseTo());
                $inventoryTo->setNote($transferHeader->getNote());
                $inventoryTo->setQuantityIn($transferDetail->getQuantity());
                $inventoryTo->setQuantityOut(0);
                $inventoryTo->setUnitPrice(0.00);
                $inventoryTo->setProduct($transferDetail->getProduct());
                $inventoryTo->setWarehouse($transferHeader->getWarehouseTo());
                $inventoryTo->setStaff($transferHeader->getStaffFirst());
                $this->inventoryRepository->add($inventoryTo);
            }
        }
    }
}