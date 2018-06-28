<?php

namespace AppBundle\Service\Transaction;

use AppBundle\Entity\Transaction\JournalVoucherHeader;
use AppBundle\Repository\Transaction\JournalVoucherHeaderRepository;

class JournalVoucherHeaderForm
{
    private $journalVoucherHeaderRepository;
    
    public function __construct(JournalVoucherHeaderRepository $journalVoucherHeaderRepository)
    {
        $this->journalVoucherHeaderRepository = $journalVoucherHeaderRepository;
    }
    
    public function initialize(JournalVoucherHeader $journalVoucherHeader, array $params = array())
    {
        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($journalVoucherHeader->getId())) {
            $lastJournalVoucherHeader = $this->journalVoucherHeaderRepository->findRecentBy($year, $month);
            $currentJournalVoucherHeader = ($lastJournalVoucherHeader === null) ? $journalVoucherHeader : $lastJournalVoucherHeader;
            $journalVoucherHeader->setCodeNumberToNext($currentJournalVoucherHeader->getCodeNumber(), $year, $month);
            
            $journalVoucherHeader->setStaff($staff);
        }
    }
    
    public function finalize(JournalVoucherHeader $journalVoucherHeader, array $params = array())
    {
        foreach ($journalVoucherHeader->getJournalVoucherDetails() as $journalVoucherDetail) {
            $journalVoucherDetail->setJournalVoucherHeader($journalVoucherHeader);
        }
        $this->sync($journalVoucherHeader);
    }
    
    private function sync(JournalVoucherHeader $journalVoucherHeader)
    {
        $journalVoucherHeader->sync();
    }
    
    public function save(JournalVoucherHeader $journalVoucherHeader)
    {
        if (empty($journalVoucherHeader->getId())) {
            $this->journalVoucherHeaderRepository->add($journalVoucherHeader, array(
                'journalVoucherDetails' => array('add' => true),
            ));
        } else {
            $this->journalVoucherHeaderRepository->update($journalVoucherHeader, array(
                'journalVoucherDetails' => array('add' => true, 'remove' => true),
            ));
        }
    }
    
    public function delete(JournalVoucherHeader $journalVoucherHeader)
    {
        $this->beforeDelete($journalVoucherHeader);
        if (!empty($journalVoucherHeader->getId())) {
            $this->journalVoucherHeaderRepository->remove($journalVoucherHeader, array(
                'journalVoucherDetails' => array('remove' => true),
            ));
        }
    }
    
    protected function beforeDelete(JournalVoucherHeader $journalVoucherHeader)
    {
        $journalVoucherHeader->getJournalVoucherDetails()->clear();
        $this->sync($journalVoucherHeader);
    }
}