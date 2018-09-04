<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\ReceiveHeader;
use AppBundle\Entity\Report\Inventory;
use AppBundle\Repository\Transaction\ReceiveHeaderRepository;
use AppBundle\Repository\Report\InventoryRepository;

class ReceiveHeaderForm
{
    private $receiveHeaderRepository;
    private $inventoryRepository;
    
    public function __construct(ReceiveHeaderRepository $receiveHeaderRepository, InventoryRepository $inventoryRepository)
    {
        $this->receiveHeaderRepository = $receiveHeaderRepository;
        $this->inventoryRepository = $inventoryRepository;
    }
    
    public function initialize(ReceiveHeader $receiveHeader, array $params = array())
    {
        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($receiveHeader->getId())) {
            $lastReceiveHeader = $this->receiveHeaderRepository->findRecentBy($year, $month);
            $currentReceiveHeader = ($lastReceiveHeader === null) ? $receiveHeader : $lastReceiveHeader;
            $receiveHeader->setCodeNumberToNext($currentReceiveHeader->getCodeNumber(), $year, $month);
            
            $receiveHeader->setStaffFirst($staff);
        }
        $receiveHeader->setStaffLast($staff);
    }
    
    public function finalize(ReceiveHeader $receiveHeader, array $params = array())
    {
        foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
            $receiveDetail->setReceiveHeader($receiveHeader);
        }
        $this->sync($receiveHeader);
    }
    
    private function sync(ReceiveHeader $receiveHeader)
    {
        $purchaseOrderHeader = $receiveHeader->getPurchaseOrderHeader();
        if ($purchaseOrderHeader !== null) {
            $purchaseOrderDetails = $purchaseOrderHeader->getPurchaseOrderDetails();
            foreach ($receiveHeader->getReceiveDetails() as $index => $receiveDetail) {
                if ($purchaseOrderDetails->containsKey($index)) {
                    $receiveDetail->setPurchaseOrderDetail($purchaseOrderDetails->get($index));
                }
            }
        }
        $receiveHeader->sync();
    }
    
    public function save(ReceiveHeader $receiveHeader)
    {
        if (empty($receiveHeader->getId())) {
            ObjectPersister::save(function() use ($receiveHeader) {
                $this->receiveHeaderRepository->add($receiveHeader, array(
                    'receiveDetails' => array('add' => true),
                ));
                $this->markInventories($receiveHeader, true);
            });
        } else {
            ObjectPersister::save(function() use ($receiveHeader) {
                $this->receiveHeaderRepository->update($receiveHeader, array(
                    'receiveDetails' => array('add' => true, 'remove' => true),
                ));
                $this->markInventories($receiveHeader, true);
            });
        }
    }
    
    public function delete(ReceiveHeader $receiveHeader)
    {
        $this->beforeDelete($receiveHeader);
        if (!empty($receiveHeader->getId())) {
            ObjectPersister::save(function() use ($receiveHeader) {
                $this->receiveHeaderRepository->remove($receiveHeader, array(
                    'receiveDetails' => array('remove' => true),
                ));
                $this->markInventories($receiveHeader, true);
            });
        }
    }
    
    protected function beforeDelete(ReceiveHeader $receiveHeader)
    {
        $receiveHeader->getReceiveDetails()->clear();
        $this->sync($receiveHeader);
    }
    
    private function markInventories(ReceiveHeader $receiveHeader, $addForHeader)
    {
        $oldInventories = $this->inventoryRepository->findBy(array(
            'transactionType' => Inventory::TRANSACTION_TYPE_RECEIVE,
            'codeNumberYear' => $receiveHeader->getCodeNumberYear(),
            'codeNumberMonth' => $receiveHeader->getCodeNumberMonth(),
            'codeNumberOrdinal' => $receiveHeader->getCodeNumberOrdinal(),
        ));
        $this->inventoryRepository->remove($oldInventories);
        foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
            if ($receiveDetail->getQuantity() > 0) {
                $inventory = new Inventory();
                $inventory->setCodeNumber($receiveHeader->getCodeNumber());
                $inventory->setTransactionDate($receiveHeader->getTransactionDate());
                $inventory->setTransactionType(Inventory::TRANSACTION_TYPE_RECEIVE);
                $inventory->setTransactionSubject($receiveHeader->getPurchaseOrderHeader()->getSupplier());
                $inventory->setNote($receiveHeader->getNote());
                $inventory->setQuantityIn($receiveDetail->getQuantity());
                $inventory->setQuantityOut(0);
                $inventory->setUnitPrice($receiveDetail->getPurchaseOrderDetail()->getUnitPrice());
                $inventory->setProduct($receiveDetail->getPurchaseOrderDetail()->getProduct());
                $inventory->setWarehouse($receiveHeader->getWarehouse());
                $inventory->setStaff($receiveHeader->getStaffFirst());
                $this->inventoryRepository->add($inventory);
            }
        }
    }
}