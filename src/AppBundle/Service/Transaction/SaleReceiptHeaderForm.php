<?php

namespace AppBundle\Service\Transaction;

use AppBundle\Entity\Transaction\SaleReceiptHeader;
use AppBundle\Repository\Transaction\SaleReceiptHeaderRepository;

class SaleReceiptHeaderForm
{
    private $saleReceiptHeaderRepository;
    
    public function __construct(SaleReceiptHeaderRepository $saleReceiptHeaderRepository)
    {
        $this->saleReceiptHeaderRepository = $saleReceiptHeaderRepository;
    }
    
    public function initialize(SaleReceiptHeader $saleReceiptHeader, array $params = array())
    {
        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($saleReceiptHeader->getId())) {
            $lastSaleReceiptHeader = $this->saleReceiptHeaderRepository->findRecentBy($year, $month);
            $currentSaleReceiptHeader = ($lastSaleReceiptHeader === null) ? $saleReceiptHeader : $lastSaleReceiptHeader;
            $saleReceiptHeader->setCodeNumberToNext($currentSaleReceiptHeader->getCodeNumber(), $year, $month);
            
            $saleReceiptHeader->setStaffFirst($staff);
        }
        $saleReceiptHeader->setStaffLast($staff);
    }
    
    public function finalize(SaleReceiptHeader $saleReceiptHeader, array $params = array())
    {
        foreach ($saleReceiptHeader->getSaleReceiptDetails() as $saleReceiptDetail) {
            $saleReceiptDetail->setSaleReceiptHeader($saleReceiptHeader);
        }
        $this->sync($saleReceiptHeader);
    }
    
    private function sync(SaleReceiptHeader $saleReceiptHeader)
    {
        $saleReceiptHeader->sync();
    }
    
    public function save(SaleReceiptHeader $saleReceiptHeader)
    {
        if (empty($saleReceiptHeader->getId())) {
            $this->saleReceiptHeaderRepository->add($saleReceiptHeader, array(
                'saleReceiptDetails' => array('add' => true),
            ));
        } else {
            $this->saleReceiptHeaderRepository->update($saleReceiptHeader, array(
                'saleReceiptDetails' => array('add' => true, 'remove' => true),
            ));
        }
    }
    
    public function delete(SaleReceiptHeader $saleReceiptHeader)
    {
        $this->beforeDelete($saleReceiptHeader);
        if (!empty($saleReceiptHeader->getId())) {
            $this->saleReceiptHeaderRepository->remove($saleReceiptHeader, array(
                'saleReceiptDetails' => array('remove' => true),
            ));
        }
    }
    
    protected function beforeDelete(SaleReceiptHeader $saleReceiptHeader)
    {
        $saleReceiptHeader->getSaleReceiptDetails()->clear();
        $this->sync($saleReceiptHeader);
    }
}