<?php

namespace AppBundle\Service\Transaction;

use AppBundle\Entity\Transaction\AdjustmentStockHeader;
use AppBundle\Repository\Transaction\AdjustmentStockHeaderRepository;

class AdjustmentStockHeaderForm
{
    private $adjustmentStockHeaderRepository;
    
    public function __construct(AdjustmentStockHeaderRepository $adjustmentStockHeaderRepository)
    {
        $this->adjustmentStockHeaderRepository = $adjustmentStockHeaderRepository;
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
        $this->sync($adjustmentStockHeader);
    }
    
    private function sync(AdjustmentStockHeader $adjustmentStockHeader)
    {
        $adjustmentStockHeader->sync();
    }
    
    public function save(AdjustmentStockHeader $adjustmentStockHeader)
    {
        if (empty($adjustmentStockHeader->getId())) {
            $this->adjustmentStockHeaderRepository->add($adjustmentStockHeader, array(
                'adjustmentStockDetails' => array('add' => true),
            ));
        } else {
            $this->adjustmentStockHeaderRepository->update($adjustmentStockHeader, array(
                'adjustmentStockDetails' => array('add' => true, 'remove' => true),
            ));
        }
    }
    
    public function delete(AdjustmentStockHeader $adjustmentStockHeader)
    {
        $this->beforeDelete($adjustmentStockHeader);
        if (!empty($adjustmentStockHeader->getId())) {
            $this->adjustmentStockHeaderRepository->remove($adjustmentStockHeader, array(
                'adjustmentStockDetails' => array('remove' => true),
            ));
        }
    }
    
    protected function beforeDelete(AdjustmentStockHeader $adjustmentStockHeader)
    {
        $adjustmentStockHeader->getAdjustmentStockDetails()->clear();
        $this->sync($adjustmentStockHeader);
    }
}