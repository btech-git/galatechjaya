<?php

namespace AppBundle\Repository\Report;

use LibBundle\Doctrine\EntityRepository;

class InventoryRepository extends EntityRepository
{
    public function getStockByProductAndWarehouse($product, $warehouse)
    {
        $query = $this->_em->createQuery('SELECT COALESCE(SUM(t.quantityIn - t.quantityOut), 0) AS stock FROM AppBundle\Entity\Report\Inventory t WHERE t.product = :product AND t.warehouse = :warehouse');
        $query->setParameter('product', $product);
        $query->setParameter('warehouse', $warehouse);
        $stock = $query->getSingleScalarResult();
        
        return $stock;
    }
}
