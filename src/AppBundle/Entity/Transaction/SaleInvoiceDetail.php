<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Master\Product;

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
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $itemName;
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
     * @ORM\Column(name="average_purchase_price", type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $averagePurchasePrice;
    /**
     * @ORM\OneToMany(targetEntity="SaleReturnDetail", mappedBy="saleInvoiceDetail")
     */
    private $saleReturnDetails;
    /**
     * @ORM\ManyToOne(targetEntity="SaleInvoiceHeader", inversedBy="saleInvoiceDetails")
     * @Assert\NotNull()
     */
    private $saleInvoiceHeader;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\Product")
     * @Assert\NotNull()
     */
    private $product;
    
    public function __construct()
    {
        $this->saleReturnDetails = new ArrayCollection();
    }
    
    public function getId() { return $this->id; }

    public function getItemName() { return $this->itemName; }
    public function setItemName($itemName) { $this->itemName = $itemName; }

    public function getQuantity() { return $this->quantity; }
    public function setQuantity($quantity) { $this->quantity = $quantity; }

    public function getUnitPrice() { return $this->unitPrice; }
    public function setUnitPrice($unitPrice) { $this->unitPrice = $unitPrice; }

    public function getDiscount() { return $this->discount; }
    public function setDiscount($discount) { $this->discount = $discount; }

    public function getTotal() { return $this->total; }
    public function setTotal($total) { $this->total = $total; }

    public function getAveragePurchasePrice() { return $this->averagePurchasePrice; }
    public function setAveragePurchasePrice($averagePurchasePrice) { $this->averagePurchasePrice = $averagePurchasePrice; }
    
    public function getSaleReturnDetails() { return $this->saleReturnDetails; }
    public function setSaleReturnDetails(Collection $saleReturnDetails) { $this->saleReturnDetails = $saleReturnDetails; }
    
    public function getSaleInvoiceHeader() { return $this->saleInvoiceHeader; }
    public function setSaleInvoiceHeader(SaleInvoiceHeader $saleInvoiceHeader = null) { $this->saleInvoiceHeader = $saleInvoiceHeader; }

    public function getProduct() { return $this->product; }
    public function setProduct(Product $product = null) { $this->product = $product; }
    
    public function sync()
    {
        $this->total = $this->quantity * $this->unitPrice * (1 - $this->discount / 100);
    }
    
    public function getAveragePurchaseTotal()
    {
        return $this->averagePurchasePrice * $this->quantity;
    }
}
