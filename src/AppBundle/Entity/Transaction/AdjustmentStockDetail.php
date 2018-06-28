<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Master\Product;

/**
 * @ORM\Table(name="transaction_adjustment_stock_detail") @ORM\Entity
 */
class AdjustmentStockDetail
{
    /**
     * @ORM\Column(name="id", type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull()
     */
    private $quantityCurrent;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $quantityAdjustment;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull()
     */
    private $quantityDifference;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\Product")
     * @Assert\NotNull()
     */
    private $product;
    /**
     * @ORM\ManyToOne(targetEntity="AdjustmentStockHeader", inversedBy="adjustmentStockDetails")
     * @Assert\NotNull()
     */
    private $adjustmentStockHeader;
    
    public function getId() { return $this->id; }

    public function getQuantityCurrent() { return $this->quantityCurrent; }
    public function setQuantityCurrent($quantityCurrent) { $this->quantityCurrent = $quantityCurrent; }

    public function getQuantityAdjustment() { return $this->quantityAdjustment; }
    public function setQuantityAdjustment($quantityAdjustment) { $this->quantityAdjustment = $quantityAdjustment; }

    public function getQuantityDifference() { return $this->quantityDifference; }
    public function setQuantityDifference($quantityDifference) { $this->quantityDifference = $quantityDifference; }

    public function getProduct() { return $this->product; }
    public function setProduct(Product $product = null) { $this->product = $product; }

    public function getAdjustmentStockHeader() { return $this->adjustmentStockHeader; }
    public function setAdjustmentStockHeader(AdjustmentStockHeader $adjustmentStockHeader = null) { $this->adjustmentStockHeader = $adjustmentStockHeader; }
    
    public function sync()
    {
        $this->quantityCurrent = 0;
        $this->quantityDifference = $this->quantityAdjustment - $this->quantityCurrent;
    }
}
