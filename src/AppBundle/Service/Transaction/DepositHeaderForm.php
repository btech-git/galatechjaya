<?php

namespace AppBundle\Service\Transaction;

use AppBundle\Entity\Transaction\DepositHeader;
use AppBundle\Repository\Transaction\DepositHeaderRepository;

class DepositHeaderForm
{
    private $depositHeaderRepository;
    
    public function __construct(DepositHeaderRepository $depositHeaderRepository)
    {
        $this->depositHeaderRepository = $depositHeaderRepository;
    }
    
    public function initialize(DepositHeader $depositHeader, array $params = array())
    {
        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($depositHeader->getId())) {
            $lastDepositHeader = $this->depositHeaderRepository->findRecentBy($year, $month);
            $currentDepositHeader = ($lastDepositHeader === null) ? $depositHeader : $lastDepositHeader;
            $depositHeader->setCodeNumberToNext($currentDepositHeader->getCodeNumber(), $year, $month);
            
            $depositHeader->setStaff($staff);
        }
    }
    
    public function finalize(DepositHeader $depositHeader, array $params = array())
    {
        foreach ($depositHeader->getDepositDetails() as $depositDetail) {
            $depositDetail->setDepositHeader($depositHeader);
        }
        $this->sync($depositHeader);
    }
    
    private function sync(DepositHeader $depositHeader)
    {
        $depositHeader->sync();
    }
    
    public function save(DepositHeader $depositHeader)
    {
        if (empty($depositHeader->getId())) {
            $this->depositHeaderRepository->add($depositHeader, array(
                'depositDetails' => array('add' => true),
            ));
        } else {
            $this->depositHeaderRepository->update($depositHeader, array(
                'depositDetails' => array('add' => true, 'remove' => true),
            ));
        }
    }
    
    public function delete(DepositHeader $depositHeader)
    {
        $this->beforeDelete($depositHeader);
        if (!empty($depositHeader->getId())) {
            $this->depositHeaderRepository->remove($depositHeader, array(
                'depositDetails' => array('remove' => true),
            ));
        }
    }
    
    protected function beforeDelete(DepositHeader $depositHeader)
    {
        $depositHeader->getDepositDetails()->clear();
        $this->sync($depositHeader);
    }
}