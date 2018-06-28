<?php

namespace AppBundle\Service\Transaction;

use AppBundle\Entity\Transaction\PurchasePaymentHeader;
use AppBundle\Repository\Transaction\PurchasePaymentHeaderRepository;

class PurchasePaymentHeaderForm
{
    private $purchasePaymentHeaderRepository;
    
    public function __construct(PurchasePaymentHeaderRepository $purchasePaymentHeaderRepository)
    {
        $this->purchasePaymentHeaderRepository = $purchasePaymentHeaderRepository;
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
    }
    
    public function save(PurchasePaymentHeader $purchasePaymentHeader)
    {
        if (empty($purchasePaymentHeader->getId())) {
            $this->purchasePaymentHeaderRepository->add($purchasePaymentHeader, array(
                'purchasePaymentDetails' => array('add' => true),
            ));
        } else {
            $this->purchasePaymentHeaderRepository->update($purchasePaymentHeader, array(
                'purchasePaymentDetails' => array('add' => true, 'remove' => true),
            ));
        }
    }
    
    public function delete(PurchasePaymentHeader $purchasePaymentHeader)
    {
        $this->beforeDelete($purchasePaymentHeader);
        if (!empty($purchasePaymentHeader->getId())) {
            $this->purchasePaymentHeaderRepository->remove($purchasePaymentHeader, array(
                'purchasePaymentDetails' => array('remove' => true),
            ));
        }
    }
    
    protected function beforeDelete(PurchasePaymentHeader $purchasePaymentHeader)
    {
        $purchasePaymentHeader->getPurchasePaymentDetails()->clear();
        $this->sync($purchasePaymentHeader);
    }
}