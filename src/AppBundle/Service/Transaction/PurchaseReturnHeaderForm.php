<?php

namespace AppBundle\Service\Transaction;

use AppBundle\Entity\Transaction\PurchaseReturnHeader;
use AppBundle\Repository\Transaction\PurchaseReturnHeaderRepository;

class PurchaseReturnHeaderForm
{
    private $purchaseReturnHeaderRepository;
    
    public function __construct(PurchaseReturnHeaderRepository $purchaseReturnHeaderRepository)
    {
        $this->purchaseReturnHeaderRepository = $purchaseReturnHeaderRepository;
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
        $purchaseReturnHeader->sync();
    }
    
    public function save(PurchaseReturnHeader $purchaseReturnHeader)
    {
        if (empty($purchaseReturnHeader->getId())) {
            $this->purchaseReturnHeaderRepository->add($purchaseReturnHeader, array(
                'purchaseReturnDetails' => array('add' => true),
            ));
        } else {
            $this->purchaseReturnHeaderRepository->update($purchaseReturnHeader, array(
                'purchaseReturnDetails' => array('add' => true, 'remove' => true),
            ));
        }
    }
    
    public function delete(PurchaseReturnHeader $purchaseReturnHeader)
    {
        $this->beforeDelete($purchaseReturnHeader);
        if (!empty($purchaseReturnHeader->getId())) {
            $this->purchaseReturnHeaderRepository->remove($purchaseReturnHeader, array(
                'purchaseReturnDetails' => array('remove' => true),
            ));
        }
    }
    
    protected function beforeDelete(PurchaseReturnHeader $purchaseReturnHeader)
    {
        $purchaseReturnHeader->getPurchaseReturnDetails()->clear();
        $this->sync($purchaseReturnHeader);
    }
}