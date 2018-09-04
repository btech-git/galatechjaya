<?php

namespace AppBundle\Repository\Master;

use LibBundle\Doctrine\EntityRepository;

class AccountRepository extends EntityRepository
{
    public function findPurchaseRecord()
    {
        return $this->find(32);
    }
    
    public function findPurchaseReturnRecord()
    {
        return $this->find(34);
    }
    
    public function findSaleRecord()
    {
        return $this->find(27);
    }
    
    public function findSaleReturnRecord()
    {
        return $this->find(29);
    }
    
    public function findInventoryRecord()
    {
        return $this->find(15);
    }
    
    public function findCogsInRecord()
    {
        return $this->find(30);
    }
    
    public function findCogsOutRecord()
    {
        return $this->find(31);
    }
}