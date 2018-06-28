<?php

namespace AppBundle\Service\Transaction;

use AppBundle\Entity\Transaction\SaleInvoiceHeader;
use AppBundle\Repository\Transaction\SaleInvoiceHeaderRepository;
use AppBundle\Repository\Transaction\PurchaseInvoiceDetailRepository;

class SaleInvoiceHeaderForm
{
    private $saleInvoiceHeaderRepository;
//    private $purchaseInvoiceDetailRepository;
    
    public function __construct(SaleInvoiceHeaderRepository $saleInvoiceHeaderRepository)//, PurchaseInvoiceDetailRepository $purchaseInvoiceDetailRepository)
    {
        $this->saleInvoiceHeaderRepository = $saleInvoiceHeaderRepository;
//        $this->purchaseInvoiceDetailRepository = $purchaseInvoiceDetailRepository;
    }
    
    public function initialize(SaleInvoiceHeader $saleInvoiceHeader, array $params = array())
    {
        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($saleInvoiceHeader->getId())) {
            $lastSaleInvoiceHeader = $this->saleInvoiceHeaderRepository->findRecentBy($year, $month);
            $currentSaleInvoiceHeader = ($lastSaleInvoiceHeader === null) ? $saleInvoiceHeader : $lastSaleInvoiceHeader;
            $saleInvoiceHeader->setCodeNumberToNext($currentSaleInvoiceHeader->getCodeNumber(), $year, $month);
            
            $saleInvoiceHeader->setStaffFirst($staff);
        }
        $saleInvoiceHeader->setStaffLast($staff);
    }
    
    public function finalize(SaleInvoiceHeader $saleInvoiceHeader, array $params = array())
    {
        foreach ($saleInvoiceHeader->getSaleInvoiceDetails() as $saleInvoiceDetail) {
            $saleInvoiceDetail->setSaleInvoiceHeader($saleInvoiceHeader);
            $averagePurchasePrice = $this->purchaseInvoiceDetailRepository->getAveragePurchasePriceByProduct($saleInvoiceDetail->getProduct());
            $saleInvoiceDetail->setAveragePurchasePrice($averagePurchasePrice);
        }
        $this->sync($saleInvoiceHeader);
    }
    
    private function sync(SaleInvoiceHeader $saleInvoiceHeader)
    {
        $saleInvoiceHeader->sync();
    }
    
    public function save(SaleInvoiceHeader $saleInvoiceHeader)
    {
        if (empty($saleInvoiceHeader->getId())) {
            $this->saleInvoiceHeaderRepository->add($saleInvoiceHeader, array(
                'saleInvoiceDetails' => array('add' => true),
            ));
        } else {
            $this->saleInvoiceHeaderRepository->update($saleInvoiceHeader, array(
                'saleInvoiceDetails' => array('add' => true, 'remove' => true),
            ));
        }
    }
    
    public function delete(SaleInvoiceHeader $saleInvoiceHeader)
    {
        $this->beforeDelete($saleInvoiceHeader);
        if (!empty($saleInvoiceHeader->getId())) {
            $this->saleInvoiceHeaderRepository->remove($saleInvoiceHeader, array(
                'saleInvoiceDetails' => array('remove' => true),
            ));
        }
    }
    
    protected function beforeDelete(SaleInvoiceHeader $saleInvoiceHeader)
    {
        $saleInvoiceHeader->getSaleInvoiceDetails()->clear();
        $this->sync($saleInvoiceHeader);
    }
}