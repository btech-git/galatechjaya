<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="transaction_purchase_return_detail") @ORM\Entity
 */
class PurchaseReturnDetail
{
    /**
     * @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $quantity;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $unitPrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $total;
    /**
     * @ORM\ManyToOne(targetEntity="PurchaseInvoiceDetail", inversedBy="purchaseReturnDetails")
     * @Assert\NotNull()
     */
    private $purchaseInvoiceDetail;
    /**
     * @ORM\ManyToOne(targetEntity="PurchaseReturnHeader", inversedBy="purchaseReturnDetails")
     * @Assert\NotNull()
     */
    private $purchaseReturnHeader;
    
    public function getId() { return $this->id; }
    
    public function getQuantity() { return $this->quantity; }
    public function setQuantity($quantity) { $this->quantity = $quantity; }

    public function getUnitPrice() { return $this->unitPrice; }
    public function setUnitPrice($unitPrice) { $this->unitPrice = $unitPrice; }

    public function getTotal() { return $this->total; }
    public function setTotal($total) { $this->total = $total; }

    public function getPurchaseInvoiceDetail() { return $this->purchaseInvoiceDetail; }
    public function setPurchaseInvoiceDetail(PurchaseInvoiceDetail $purchaseInvoiceDetail = null) { $this->purchaseInvoiceDetail = $purchaseInvoiceDetail; }

    public function getPurchaseReturnHeader() { return $this->purchaseReturnHeader; }
    public function setPurchaseReturnHeader(PurchaseReturnHeader $purchaseReturnHeader = null) { $this->purchaseReturnHeader = $purchaseReturnHeader; }
    
    public function sync()
    {
        $this->total = $this->quantity * $this->unitPrice;
    }
}
