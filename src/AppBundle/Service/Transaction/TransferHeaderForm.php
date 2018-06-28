<?php

namespace AppBundle\Service\Transaction;

use AppBundle\Entity\Transaction\TransferHeader;
use AppBundle\Repository\Transaction\TransferHeaderRepository;

class TransferHeaderForm
{
    private $transferHeaderRepository;
    
    public function __construct(TransferHeaderRepository $transferHeaderRepository)
    {
        $this->transferHeaderRepository = $transferHeaderRepository;
    }
    
    public function initialize(TransferHeader $transferHeader, array $params = array())
    {
        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($transferHeader->getId())) {
            $lastTransferHeader = $this->transferHeaderRepository->findRecentBy($year, $month);
            $currentTransferHeader = ($lastTransferHeader === null) ? $transferHeader : $lastTransferHeader;
            $transferHeader->setCodeNumberToNext($currentTransferHeader->getCodeNumber(), $year, $month);
            
            $transferHeader->setStaffFirst($staff);
        }
        $transferHeader->setStaffLast($staff);
    }
    
    public function finalize(TransferHeader $transferHeader, array $params = array())
    {
        foreach ($transferHeader->getTransferDetails() as $transferDetail) {
            $transferDetail->setTransferHeader($transferHeader);
        }
        $this->sync($transferHeader);
    }
    
    private function sync(TransferHeader $transferHeader)
    {
        $transferHeader->sync();
    }
    
    public function save(TransferHeader $transferHeader)
    {
        if (empty($transferHeader->getId())) {
            $this->transferHeaderRepository->add($transferHeader, array(
                'transferDetails' => array('add' => true),
            ));
        } else {
            $this->transferHeaderRepository->update($transferHeader, array(
                'transferDetails' => array('add' => true, 'remove' => true),
            ));
        }
    }
    
    public function delete(TransferHeader $transferHeader)
    {
        $this->beforeDelete($transferHeader);
        if (!empty($transferHeader->getId())) {
            $this->transferHeaderRepository->remove($transferHeader, array(
                'transferDetails' => array('remove' => true),
            ));
        }
    }
    
    protected function beforeDelete(TransferHeader $transferHeader)
    {
        $transferHeader->getTransferDetails()->clear();
        $this->sync($transferHeader);
    }
}