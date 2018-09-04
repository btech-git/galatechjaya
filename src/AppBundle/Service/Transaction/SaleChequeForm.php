<?php

namespace AppBundle\Service\Transaction;

use LibBundle\Doctrine\ObjectPersister;
use AppBundle\Entity\Transaction\SaleCheque;
use AppBundle\Repository\Transaction\SaleChequeRepository;

class SaleChequeForm
{
    private $saleChequeRepository;
    
    public function __construct(SaleChequeRepository $saleChequeRepository)
    {
        $this->saleChequeRepository = $saleChequeRepository;
    }
    
    public function initialize(SaleCheque $saleCheque, array $params = array())
    {
        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($saleCheque->getId())) {
            $lastSaleCheque = $this->saleChequeRepository->findRecentBy($year, $month);
            $currentSaleCheque = ($lastSaleCheque === null) ? $saleCheque : $lastSaleCheque;
            $saleCheque->setCodeNumberToNext($currentSaleCheque->getCodeNumber(), $year, $month);
            
            $saleCheque->setStaffFirst($staff);
        }
        $saleCheque->setStaffLast($staff);
    }
    
    public function finalize(SaleCheque $saleCheque, array $params = array())
    {
        $this->sync($saleCheque);
    }
    
    private function sync(SaleCheque $saleCheque)
    {
        $saleCheque->sync();
    }
    
    public function save(SaleCheque $saleCheque)
    {
        if (empty($saleCheque->getId())) {
            ObjectPersister::save(function() use ($saleCheque) {
                $this->saleChequeRepository->add($saleCheque);
            });
        } else {
            ObjectPersister::save(function() use ($saleCheque) {
                $this->saleChequeRepository->update($saleCheque);
            });
        }
    }
    
    public function delete(SaleCheque $saleCheque)
    {
        $this->beforeDelete($saleCheque);
        if (!empty($saleCheque->getId())) {
            ObjectPersister::save(function() use ($saleCheque) {
                $this->saleChequeRepository->remove($saleCheque);
            });
        }
    }
    
    protected function beforeDelete(SaleCheque $saleCheque)
    {
        $this->sync($saleCheque);
    }
}