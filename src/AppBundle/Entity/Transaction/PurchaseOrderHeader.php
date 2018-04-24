<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Entity\Common\CodeNumberEntity;
use AppBundle\Entity\Admin\Staff;
use AppBundle\Entity\Master\Supplier;

/**
 * @ORM\Table(name="transaction_purchase_order_header")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\PurchaseOrderHeaderRepository")
 */
class PurchaseOrderHeader extends CodeNumberEntity
{
    /**
     * @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="date")
     * @Assert\NotNull() @Assert\Date()
     */
    private $transactionDate;
    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull()
     */
    private $note;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $totalQuantity;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $subTotal;
    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $discountPercentage;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $discountNominal;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $taxNominal;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $shippingFee;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $grandTotal;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isTax;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Admin\Staff")
     * @Assert\NotNull()
     */
    private $staffFirst;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Admin\Staff")
     * @Assert\NotNull()
     */
    private $staffLast;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\Supplier")
     * @Assert\NotNull()
     */
    private $supplier;
    /**
     * @ORM\OneToMany(targetEntity="ReceiveHeader", mappedBy="purchaseOrderHeader")
     */
    private $receiveHeaders;
    /**
     * @ORM\OneToMany(targetEntity="PurchaseOrderDetail", mappedBy="purchaseOrderHeader")
     * @Assert\Valid() @Assert\Count(min=1)
     */
    private $purchaseOrderDetails;
    
    public function __construct()
    {
        $this->purchaseOrderDetails = new ArrayCollection();
        $this->receiveOrders = new ArrayCollection();
    }
    
    public function getCodeNumberConstant() { return 'PO'; }
    
    public function getId() { return $this->id; }
    
    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate($transactionDate) { $this->transactionDate = $transactionDate; }

    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }

    public function getTotalQuantity() { return $this->totalQuantity; }
    public function setTotalQuantity($totalQuantity) { $this->totalQuantity = $totalQuantity; }

    public function getSubTotal() { return $this->subTotal; }
    public function setSubTotal($subTotal) { $this->subTotal = $subTotal; }

    public function getDiscountPercentage() { return $this->discountPercentage; }
    public function setDiscountPercentage($discountPercentage) { $this->discountPercentage = $discountPercentage; }

    public function getDiscountNominal() { return $this->discountNominal; }
    public function setDiscountNominal($discountNominal) { $this->discountNominal = $discountNominal; }

    public function getTaxNominal() { return $this->taxNominal; }
    public function setTaxNominal($taxNominal) { $this->taxNominal = $taxNominal; }

    public function getShippingFee() { return $this->shippingFee; }
    public function setShippingFee($shippingFee) { $this->shippingFee = $shippingFee; }

    public function getGrandTotal() { return $this->grandTotal; }
    public function setGrandTotal($grandTotal) { $this->grandTotal = $grandTotal; }

    public function getIsTax() { return $this->isTax; }
    public function setIsTax($isTax) { $this->isTax = $isTax; }

    public function getStaffFirst() { return $this->staffFirst; }
    public function setStaffFirst(Staff $staffFirst = null) { $this->staffFirst = $staffFirst; }

    public function getStaffLast() { return $this->staffLast; }
    public function setStaffLast(Staff $staffLast = null) { $this->staffLast = $staffLast; }

    public function getSupplier() { return $this->supplier; }
    public function setSupplier(Supplier $supplier = null) { $this->supplier = $supplier; }

    public function getReceiveHeaders() { return $this->receiveHeaders; }
    public function setReceiveHeaders(Collection $receiveHeaders) { $this->receiveHeaders = $receiveHeaders; }

    public function getPurchaseOrderDetails() { return $this->purchaseOrderDetails; }
    public function setPurchaseOrderDetails(Collection $purchaseOrderDetails) { $this->purchaseOrderDetails = $purchaseOrderDetails; }
    
    public function sync()
    {
        $totalQuantity = 0.00;
        $subTotal = 0.00;
        foreach ($this->purchaseOrderDetails as $purchaseOrderDetail) {
            $purchaseOrderDetail->sync();
            $totalQuantity += $purchaseOrderDetail->getQuantity();
            $subTotal += $purchaseOrderDetail->getTotal();
        }
        $this->totalQuantity = $totalQuantity;
        $this->subTotal = $subTotal;
        $this->discountNominal = $this->subTotal * $this->discountPercentage / 100;
        $this->taxNominal = ($this->isTax ? ($this->subTotal - $this->discountNominal) * 10 / 100 : 0);
        $this->grandTotal = $this->subTotal - $this->discountNominal + $this->taxNominal + $this->shippingFee;
    }
}
