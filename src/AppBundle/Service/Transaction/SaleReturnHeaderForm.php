<?php

namespace AppBundle\Service\Transaction;

use AppBundle\Entity\Transaction\SaleReturnHeader;
use AppBundle\Repository\Transaction\SaleReturnHeaderRepository;

class SaleReturnHeaderForm
{
    private $saleReturnHeaderRepository;
    
    public function __construct(SaleReturnHeaderRepository $saleReturnHeaderRepository)
    {
        $this->saleReturnHeaderRepository = $saleReturnHeaderRepository;
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
                    $saleInvoiceDetail = $saleInvoiceDetails->get($index);
                    $saleReturnDetail->setSaleInvoiceDetail($saleInvoiceDetail);
                    $saleReturnDetail->setUnitPrice($saleInvoiceDetail->getUnitPrice());
                }
            }
        }
        $saleReturnHeader->sync();
    }
    
    public function save(SaleReturnHeader $saleReturnHeader)
    {
        if (empty($saleReturnHeader->getId())) {
            $this->saleReturnHeaderRepository->add($saleReturnHeader, array(
                'saleReturnDetails' => array('add' => true),
            ));
        } else {
            $this->saleReturnHeaderRepository->update($saleReturnHeader, array(
                'saleReturnDetails' => array('add' => true, 'remove' => true),
            ));
        }
    }
    
    public function delete(SaleReturnHeader $saleReturnHeader)
    {
        $this->beforeDelete($saleReturnHeader);
        if (!empty($saleReturnHeader->getId())) {
            $this->saleReturnHeaderRepository->remove($saleReturnHeader, array(
                'saleReturnDetails' => array('remove' => true),
            ));
        }
    }
    
    protected function beforeDelete(SaleReturnHeader $saleReturnHeader)
    {
        $saleReturnHeader->getSaleReturnDetails()->clear();
        $this->sync($saleReturnHeader);
    }
}