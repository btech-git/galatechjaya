<?php

namespace AppBundle\Repository\Transaction;

use LibBundle\Doctrine\EntityRepository;

class PurchasePaymentHeaderRepository extends EntityRepository
{
    public function findRecentBy($year, $month)
    {
        $query = $this->_em->createQuery('SELECT t FROM AppBundle\Entity\Transaction\PurchasePaymentHeader t WHERE t.codeNumberMonth = :codeNumberMonth AND t.codeNumberYear = :codeNumberYear ORDER BY t.codeNumberOrdinal DESC');
        $query->setParameter('codeNumberMonth', $month);
        $query->setParameter('codeNumberYear', $year);
        $query->setMaxResults(1);
        $lastPurchasePaymentHeader = $query->getOneOrNullResult();
        
        return $lastPurchasePaymentHeader;
    }
}