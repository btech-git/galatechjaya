<?php

namespace AppBundle\Service\Transaction;

use AppBundle\Entity\Transaction\PurchaseOrderHeader;
use AppBundle\Repository\Transaction\PurchaseOrderHeaderRepository;

class PurchaseOrderHeaderForm
{
    private $purchaseOrderHeaderRepository;
    
    public function __construct(PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository)
    {
        $this->purchaseOrderHeaderRepository = $purchaseOrderHeaderRepository;
    }
    
    public function initialize(PurchaseOrderHeader $purchaseOrderHeader, array $params = array())
    {
        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($purchaseOrderHeader->getId())) {
            $lastPurchaseOrderHeader = $this->purchaseOrderHeaderRepository->findRecentBy($year, $month);
            $currentPurchaseOrderHeader = ($lastPurchaseOrderHeader === null) ? $purchaseOrderHeader : $lastPurchaseOrderHeader;
            $purchaseOrderHeader->setCodeNumberToNext($currentPurchaseOrderHeader->getCodeNumber(), $year, $month);
            
            $purchaseOrderHeader->setStaffFirst($staff);
        }
        $purchaseOrderHeader->setStaffLast($staff);
    }
    
    public function finalize(PurchaseOrderHeader $purchaseOrderHeader, array $params = array())
    {
        foreach ($purchaseOrderHeader->getPurchaseOrderDetails() as $purchaseOrderDetail) {
            $purchaseOrderDetail->setPurchaseOrderHeader($purchaseOrderHeader);
        }
        $this->sync($purchaseOrderHeader);
    }
    
    private function sync(PurchaseOrderHeader $purchaseOrderHeader)
    {
        $purchaseOrderHeader->sync();
        foreach ($purchaseOrderHeader->getPurchaseOrderDetails() as $purchaseOrderDetail) {
            $purchaseOrderDetail->setQuantityRemaining($purchaseOrderDetail->getQuantity());
        }
    }
    
    public function save(PurchaseOrderHeader $purchaseOrderHeader)
    {
        if (empty($purchaseOrderHeader->getId())) {
            $this->purchaseOrderHeaderRepository->add($purchaseOrderHeader, array(
                'purchaseOrderDetails' => array('add' => true),
            ));
        } else {
            $this->purchaseOrderHeaderRepository->update($purchaseOrderHeader, array(
                'purchaseOrderDetails' => array('add' => true, 'remove' => true),
            ));
        }
    }
    
    public function delete(PurchaseOrderHeader $purchaseOrderHeader)
    {
        $this->beforeDelete($purchaseOrderHeader);
        if (!empty($purchaseOrderHeader->getId())) {
            $this->purchaseOrderHeaderRepository->remove($purchaseOrderHeader, array(
                'purchaseOrderDetails' => array('remove' => true),
            ));
        }
    }
    
    protected function beforeDelete(PurchaseOrderHeader $purchaseOrderHeader)
    {
        $purchaseOrderHeader->getPurchaseOrderDetails()->clear();
        $this->sync($purchaseOrderHeader);
    }
}