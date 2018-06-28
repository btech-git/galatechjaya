<?php

namespace AppBundle\Service\Transaction;

use AppBundle\Entity\Transaction\SalePaymentHeader;
use AppBundle\Repository\Transaction\SalePaymentHeaderRepository;

class SalePaymentHeaderForm
{
    private $salePaymentHeaderRepository;
    
    public function __construct(SalePaymentHeaderRepository $salePaymentHeaderRepository)
    {
        $this->salePaymentHeaderRepository = $salePaymentHeaderRepository;
    }
    
    public function initialize(SalePaymentHeader $salePaymentHeader, array $params = array())
    {
        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($salePaymentHeader->getId())) {
            $lastSalePaymentHeader = $this->salePaymentHeaderRepository->findRecentBy($year, $month);
            $currentSalePaymentHeader = ($lastSalePaymentHeader === null) ? $salePaymentHeader : $lastSalePaymentHeader;
            $salePaymentHeader->setCodeNumberToNext($currentSalePaymentHeader->getCodeNumber(), $year, $month);
            
            $salePaymentHeader->setStaffFirst($staff);
        }
        $salePaymentHeader->setStaffLast($staff);
    }
    
    public function finalize(SalePaymentHeader $salePaymentHeader, array $params = array())
    {
        foreach ($salePaymentHeader->getSalePaymentDetails() as $salePaymentDetail) {
            $salePaymentDetail->setSalePaymentHeader($salePaymentHeader);
        }
        $this->sync($salePaymentHeader);
    }
    
    private function sync(SalePaymentHeader $salePaymentHeader)
    {
        $salePaymentHeader->sync();
    }
    
    public function save(SalePaymentHeader $salePaymentHeader)
    {
        if (empty($salePaymentHeader->getId())) {
            $this->salePaymentHeaderRepository->add($salePaymentHeader, array(
                'salePaymentDetails' => array('add' => true),
            ));
        } else {
            $this->salePaymentHeaderRepository->update($salePaymentHeader, array(
                'salePaymentDetails' => array('add' => true, 'remove' => true),
            ));
        }
    }
    
    public function delete(SalePaymentHeader $salePaymentHeader)
    {
        $this->beforeDelete($salePaymentHeader);
        if (!empty($salePaymentHeader->getId())) {
            $this->salePaymentHeaderRepository->remove($salePaymentHeader, array(
                'salePaymentDetails' => array('remove' => true),
            ));
        }
    }
    
    protected function beforeDelete(SalePaymentHeader $salePaymentHeader)
    {
        $salePaymentHeader->getSalePaymentDetails()->clear();
        $this->sync($salePaymentHeader);
    }
}