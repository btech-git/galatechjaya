<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Entity\Common\CodeNumberEntity;
use AppBundle\Entity\Master\Product;
use AppBundle\Entity\Admin\Staff;

/**
 * @ORM\Table(name="transaction_sale_order_detail")
 * @ORM\Entity
 */
class SaleOrderDetail
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
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $discount;
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
     * @ORM\OneToMany(targetEntity="DeliveryDetail", mappedBy="saleOrderDetail")
     */
    private $deliveryDetails;
    /**
     * @ORM\ManyToOne(targetEntity="SaleOrderHeader", inversedBy="saleOrderDetails")
     * @Assert\NotNull()
     */
    private $saleOrderHeader;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\Product")
     * @Assert\NotNull()
     */
    private $product;
    
    public function __construct()
    {
        $this->deliveryDetails = new ArrayCollection();
    }
    
    public function getId() { return $this->id; }

    public function getItemName() { return $this->itemName; }
    public function setItemName($itemName) { $this->itemName = $itemName; }

    public function getQuantity() { return $this->quantity; }
    public function setQuantity($quantity) { $this->quantity = $quantity; }

    public function getDiscount() { return $this->discount; }
    public function setDiscount($discount) { $this->discount = $discount; }

    public function getUnitPrice() { return $this->unitPrice; }
    public function setUnitPrice($unitPrice) { $this->unitPrice = $unitPrice; }

    public function getTotal() { return $this->total; }
    public function setTotal($total) { $this->total = $total; }

    public function getDeliveryDetails() { return $this->deliveryDetails; }
    public function setDeliveryDetails(Collection $deliveryDetails) { $this->deliveryDetails = $deliveryDetails; }

    public function getSaleOrderHeader() { return $this->saleOrderHeader; }
    public function setSaleOrderHeader(SaleOrderHeader $saleOrderHeader = null) { $this->saleOrderHeader = $saleOrderHeader; }

    public function getProduct() { return $this->product; }
    public function setProduct(Product $product = null) { $this->product = $product; }
}
