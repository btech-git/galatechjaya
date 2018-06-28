<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="transaction_purchase_invoice_detail")
 * @ORM\Entity
 */
class PurchaseInvoiceDetail
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
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $discount;
    /**
     * @ORM\Column(precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $total;
    /**
     * @ORM\ManyToOne(targetEntity="PurchaseInvoiceHeader", inversedBy="purchaseInvoiceDetails")
     * @Assert\NotNull()
     */
    private $purchaseInvoiceHeader;
    /**
     * @ORM\OneToOne(targetEntity="ReceiveDetail", inversedBy="purchaseInvoiceDetail")
     * @Assert\NotNull()
     */
    private $receiveDetail;
    /**
     * @ORM\OneToMany(targetEntity="PurchaseReturnDetail", mappedBy="purchaseInvoiceDetail")
     */
    private $purchaseReturnDetails;
    
    public function __construct()
    {
        $this->purchaseReturnDetails = new ArrayCollection();
    }
    
    public function getId() { return $this->id; }

    public function getQuantity() { return $this->quantity; }
    public function setQuantity($quantity) { $this->quantity = $quantity; }

    public function getUnitPrice() { return $this->unitPrice; }
    public function setUnitPrice($unitPrice) { $this->unitPrice = $unitPrice; }

    public function getDiscount() { return $this->discount; }
    public function setDiscount($discount) { $this->discount = $discount; }

    public function getTotal() { return $this->total; }
    public function setTotal($total) { $this->total = $total; }

    public function getPurchaseInvoiceHeader() { return $this->purchaseInvoiceHeader; }
    public function setPurchaseInvoiceHeader(PurchaseInvoiceHeader $purchaseInvoiceHeader = null) { $this->purchaseInvoiceHeader = $purchaseInvoiceHeader; }

    public function getReceiveDetail() { return $this->receiveDetail; }
    public function setReceiveDetail(ReceiveDetail $receiveDetail = null) { $this->receiveDetail = $receiveDetail; }

    public function getPurchaseReturnDetails() { return $this->purchaseReturnDetails; }
    public function setPurchaseReturnDetails(Collection $purchaseReturnDetails) { $this->purchaseReturnDetails = $purchaseReturnDetails; }
    
    public function sync()
    {
        $this->total = $this->quantity * $this->unitPrice * (1 - $this->discount / 100);
    }
}
