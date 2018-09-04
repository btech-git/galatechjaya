<?php

namespace AppBundle\Entity\Master;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="master_product") @ORM\Entity
 * @UniqueEntity("code")
 * @UniqueEntity({"name", "size"})
 */
class Product
{
    /**
     * @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=20, unique=true)
     * @Assert\NotBlank()
     */
    private $code;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $name;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank()
     */
    private $size;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $weightedPurchasePrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $sellingPrice;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isActive = true;
    /**
     * @ORM\ManyToOne(targetEntity="ProductCategory", inversedBy="products")
     * @Assert\NotNull()
     */
    private $productCategory;
    /**
     * @ORM\ManyToOne(targetEntity="Unit", inversedBy="products")
     * @Assert\NotNull()
     */
    private $unit;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Transaction\PurchaseOrderDetail", mappedBy="product")
     */
    private $purchaseOrderDetails;
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Transaction\SaleInvoiceDetail", mappedBy="product")
     */
    private $saleInvoiceDetails;
    
    public function __construct()
    {
        $this->purchaseOrderDetails = new ArrayCollection();
        $this->saleInvoiceDetails = new ArrayCollection();
    }
    
    public function __toString()
    {
        return $this->name . ' - ' . $this->size;
    }
    
    public function getId() { return $this->id; }

    public function getCode() { return $this->code; }
    public function setCode($code) { $this->code = $code; }

    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }

    public function getSize() { return $this->size; }
    public function setSize($size) { $this->size = $size; }

    public function getWeightedPurchasePrice() { return $this->weightedPurchasePrice; }
    public function setWeightedPurchasePrice($weightedPurchasePrice) { $this->weightedPurchasePrice = $weightedPurchasePrice; }

    public function getSellingPrice() { return $this->sellingPrice; }
    public function setSellingPrice($sellingPrice) { $this->sellingPrice = $sellingPrice; }

    public function getIsActive() { return $this->isActive; }
    public function setIsActive($isActive) { $this->isActive = $isActive; }

    public function getProductCategory() { return $this->productCategory; }
    public function setProductCategory(ProductCategory $productCategory = null) { $this->productCategory = $productCategory; }

    public function getUnit() { return $this->unit; }
    public function setUnit(Unit $unit = null) { $this->unit = $unit; }
    
    public function getPurchaseOrderDetails() { return $this->purchaseOrderDetails; }
    public function setPurchaseOrderDetails(Collection $purchaseOrderDetails) { $this->purchaseOrderDetails = $purchaseOrderDetails; }
    
    public function getSaleInvoiceDetails() { return $this->saleInvoiceDetails; }
    public function setSaleInvoiceDetails(Collection $saleInvoiceDetails) { $this->saleInvoiceDetails = $saleInvoiceDetails; }
}
