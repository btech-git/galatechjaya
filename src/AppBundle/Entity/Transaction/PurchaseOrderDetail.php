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
 * @ORM\Table(name="transaction_purchase_order_detail")
 * @ORM\Entity
 */
class PurchaseOrderDetail
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
     * @ORM\OneToMany(targetEntity="ReceiveDetail", mappedBy="purchaseOrderDetail")
     */
    private $receiveDetails;
    /**
     * @ORM\ManyToOne(targetEntity="PurchaseOrderHeader", inversedBy="purchaseOrderDetails")
     * @Assert\NotNull()
     */
    private $purchaseOrderHeader;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\Product")
     * @Assert\NotNull()
     */
    private $product;
    
    public function __construct()
    {
        $this->receiveDetails = new ArrayCollection();
    }
    
    public function getId() { return $this->id; }

    public function getQuantity() { return $this->quantity; }
    public function setQuantity($quantity) { $this->quantity = $quantity; }

    public function getDiscount() { return $this->discount; }
    public function setDiscount($discount) { $this->discount = $discount; }

    public function getUnitPrice() { return $this->unitPrice; }
    public function setUnitPrice($unitPrice) { $this->unitPrice = $unitPrice; }

    public function getTotal() { return $this->total; }
    public function setTotal($total) { $this->total = $total; }

    public function getReceiveDetails() { return $this->receiveDetails; }
    public function setReceiveDetails(Collection $receiveDetails) { $this->receiveDetails = $receiveDetails; }

    public function getPurchaseOrderHeader() { return $this->purchaseOrderHeader; }
    public function setPurchaseOrderHeader(PurchaseOrderHeader $purchaseOrderHeader = null) { $this->purchaseOrderHeader = $purchaseOrderHeader; }

    public function getProduct() { return $this->product; }
    public function setProduct(Product $product = null) { $this->product = $product; }
}
