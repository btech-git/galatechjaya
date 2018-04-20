<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="transaction_sale_invoice_detail")
 * @ORM\Entity
 */
class SaleInvoiceDetail
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
     * @ORM\ManyToOne(targetEntity="SaleInvoiceHeader", inversedBy="saleInvoiceDetails")
     * @Assert\NotNull()
     */
    private $saleInvoiceHeader;
    /**
     * @ORM\OneToOne(targetEntity="DeliveryDetail", inversedBy="saleInvoiceDetail")
     * @Assert\NotNull()
     */
    private $deliveryDetail;
    /**
     * @ORM\OneToMany(targetEntity="SaleReturnDetail", mappedBy="saleInvoiceDetail")
     * @Assert\Valid() @Assert\Count(min=1)
     */
    private $saleReturnDetails;
    
    public function __construct()
    {
        $this->saleReturnDetails = new ArrayCollection();
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

    public function getSaleInvoiceHeader() { return $this->saleInvoiceHeader; }
    public function setSaleInvoiceHeader(SaleInvoiceHeader $saleInvoiceHeader = null) { $this->saleInvoiceHeader = $saleInvoiceHeader; }

    public function getDeliveryDetail() { return $this->deliveryDetail; }
    public function setDeliveryDetail(DeliveryDetail $deliveryDetail = null) { $this->deliveryDetail = $deliveryDetail; }

    public function getSaleReturnDetails() { return $this->saleReturnDetails; }
    public function setSaleReturnDetails(Collection $saleReturnDetails) { $this->saleReturnDetails = $saleReturnDetails; }
}
