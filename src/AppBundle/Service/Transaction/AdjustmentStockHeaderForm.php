<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\AdjustmentStockHeader;
use AppBundle\Entity\Report\Inventory;
use AppBundle\Repository\Transaction\AdjustmentStockHeaderRepository;
use AppBundle\Repository\Report\InventoryRepository;

class AdjustmentStockHeaderForm
{
    private $adjustmentStockHeaderRepository;
    private $inventoryRepository;
    
    public function __construct(AdjustmentStockHeaderRepository $adjustmentStockHeaderRepository, InventoryRepository $inventoryRepository)
    {
        $this->adjustmentStockHeaderRepository = $adjustmentStockHeaderRepository;
        $this->inventoryRepository = $inventoryRepository;
    }
    
    public function initialize(AdjustmentStockHeader $adjustmentStockHeader, array $params = array())
    {
        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($adjustmentStockHeader->getId())) {
            $lastAdjustmentStockHeader = $this->adjustmentStockHeaderRepository->findRecentBy($year, $month);
            $currentAdjustmentStockHeader = ($lastAdjustmentStockHeader === null) ? $adjustmentStockHeader : $lastAdjustmentStockHeader;
            $adjustmentStockHeader->setCodeNumberToNext($currentAdjustmentStockHeader->getCodeNumber(), $year, $month);
            
            $adjustmentStockHeader->setStaff($staff);
        }
    }
    
    public function finalize(AdjustmentStockHeader $adjustmentStockHeader, array $params = array())
    {
        foreach ($adjustmentStockHeader->getAdjustmentStockDetails() as $adjustmentStockDetail) {
            $adjustmentStockDetail->setAdjustmentStockHeader($adjustmentStockHeader);
        }
        if (empty($adjustmentStockHeader->getId())) {
            foreach ($adjustmentStockHeader->getAdjustmentStockDetails() as $adjustmentStockDetail) {
                $stock = $this->inventoryRepository->getStockByProductAndWarehouse($adjustmentStockDetail->getProduct(), $adjustmentStockHeader->getWarehouse());
                $adjustmentStockDetail->setQuantityCurrent($stock);
            }
        }
        $this->sync($adjustmentStockHeader);
    }
    
    private function sync(AdjustmentStockHeader $adjustmentStockHeader)
    {
        $adjustmentStockHeader->sync();
    }
    
    public function save(AdjustmentStockHeader $adjustmentStockHeader)
    {
        if (empty($adjustmentStockHeader->getId())) {
            ObjectPersister::save(function() use ($adjustmentStockHeader) {
                $this->adjustmentStockHeaderRepository->add($adjustmentStockHeader, array(
                    'adjustmentStockDetails' => array('add' => true),
                ));
                $this->markInventories($adjustmentStockHeader, true);
            });
        } else {
            ObjectPersister::save(function() use ($adjustmentStockHeader) {
                $this->adjustmentStockHeaderRepository->update($adjustmentStockHeader, array(
                    'adjustmentStockDetails' => array('add' => true, 'remove' => true),
                ));
                $this->markInventories($adjustmentStockHeader, true);
            });
        }
    }
    
    public function delete(AdjustmentStockHeader $adjustmentStockHeader)
    {
        $this->beforeDelete($adjustmentStockHeader);
        if (!empty($adjustmentStockHeader->getId())) {
            ObjectPersister::save(function() use ($adjustmentStockHeader) {
                $this->adjustmentStockHeaderRepository->remove($adjustmentStockHeader, array(
                    'adjustmentStockDetails' => array('remove' => true),
                ));
                $this->markInventories($adjustmentStockHeader, true);
            });
        }
    }
    
    protected function beforeDelete(AdjustmentStockHeader $adjustmentStockHeader)
    {
        $adjustmentStockHeader->getAdjustmentStockDetails()->clear();
        $this->sync($adjustmentStockHeader);
    }
    
    private function markInventories(AdjustmentStockHeader $adjustmentStockHeader, $addForHeader)
    {
        $oldInventories = $this->inventoryRepository->findBy(array(
            'transactionType' => Inventory::TRANSACTION_TYPE_ADJUSTMENT,
            'codeNumberYear' => $adjustmentStockHeader->getCodeNumberYear(),
            'codeNumberMonth' => $adjustmentStockHeader->getCodeNumberMonth(),
            'codeNumberOrdinal' => $adjustmentStockHeader->getCodeNumberOrdinal(),
        ));
        $this->inventoryRepository->remove($oldInventories);
        foreach ($adjustmentStockHeader->getAdjustmentStockDetails() as $adjustmentStockDetail) {
            if ($adjustmentStockDetail->getQuantityDifference() > 0) {
                $inventory = new Inventory();
                $inventory->setCodeNumber($adjustmentStockHeader->getCodeNumber());
                $inventory->setTransactionDate($adjustmentStockHeader->getTransactionDate());
                $inventory->setTransactionType(Inventory::TRANSACTION_TYPE_ADJUSTMENT);
                $inventory->setTransactionSubject($adjustmentStockHeader->getWarehouse());
                $inventory->setNote($adjustmentStockHeader->getNote());
                $inventory->setQuantityIn($adjustmentStockDetail->getQuantityDifference());
                $inventory->setQuantityOut(0);
                $inventory->setUnitPrice(0.00);
                $inventory->setProduct($adjustmentStockDetail->getProduct());
                $inventory->setWarehouse($adjustmentStockHeader->getWarehouse());
                $inventory->setStaff($adjustmentStockHeader->getStaff());
                $this->inventoryRepository->add($inventory);
            }
        }
    }
}