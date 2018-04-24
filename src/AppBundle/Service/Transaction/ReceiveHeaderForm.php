<?php

namespace AppBundle\Service\Transaction;

use AppBundle\Entity\Transaction\ReceiveHeader;
use AppBundle\Entity\Transaction\ReceiveDetail;
use AppBundle\Repository\Transaction\ReceiveHeaderRepository;

class ReceiveHeaderForm
{
    private $receiveHeaderRepository;
    
    public function __construct(ReceiveHeaderRepository $receiveHeaderRepository)
    {
        $this->receiveHeaderRepository = $receiveHeaderRepository;
    }
    
    public function initialize(ReceiveHeader $receiveHeader, array $params = array())
    {
        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($receiveHeader->getId())) {
            $lastReceiveHeader = $this->receiveHeaderRepository->findRecentBy($year, $month);
            $currentReceiveHeader = ($lastReceiveHeader === null) ? $receiveHeader : $lastReceiveHeader;
            $receiveHeader->setCodeNumberToNext($currentReceiveHeader->getCodeNumber(), $year, $month);
            
            $receiveHeader->setStaffFirst($staff);
        }
        $receiveHeader->setStaffLast($staff);
    }
    
    public function finalize(ReceiveHeader $receiveHeader, array $params = array())
    {
        foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
            $receiveDetail->setReceiveHeader($receiveHeader);
        }
        $this->sync($receiveHeader);
    }
    
    private function sync(ReceiveHeader $receiveHeader)
    {
        $purchaseOrderHeader = $receiveHeader->getPurchaseOrderHeader();
        if ($purchaseOrderHeader !== null) {
            $purchaseOrderDetails = $purchaseOrderHeader->getPurchaseOrderDetails();
            foreach ($receiveHeader->getReceiveDetails() as $index => $receiveDetail) {
                if ($purchaseOrderDetails->containsKey($index)) {
                    $receiveDetail->setPurchaseOrderDetail($purchaseOrderDetails->get($index));
                }
            }
        }
        $receiveHeader->sync();
    }
    
    public function save(ReceiveHeader $receiveHeader)
    {
        if (empty($receiveHeader->getId())) {
            $this->receiveHeaderRepository->add($receiveHeader, array(
                'receiveDetails' => array('add' => true),
            ));
        } else {
            $this->receiveHeaderRepository->update($receiveHeader, array(
                'receiveDetails' => array('add' => true, 'remove' => true),
            ));
        }
    }
    
    public function delete(ReceiveHeader $receiveHeader)
    {
        $this->beforeDelete($receiveHeader);
        if (!empty($receiveHeader->getId())) {
            $this->receiveHeaderRepository->remove($receiveHeader, array(
                'receiveDetails' => array('remove' => true),
            ));
        }
    }
    
    protected function beforeDelete(ReceiveHeader $receiveHeader)
    {
        $receiveHeader->getReceiveDetails()->clear();
        $this->sync($receiveHeader);
    }
}