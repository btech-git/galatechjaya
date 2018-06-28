<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Common\CodeNumberEntity;
use AppBundle\Entity\Admin\Staff;

/**
 * @ORM\Table(name="transaction_purchase_return_header")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\PurchaseReturnHeaderRepository")
 */
class PurchaseReturnHeader extends CodeNumberEntity
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
     * @ORM\ManyToOne(targetEntity="PurchaseInvoiceHeader", inversedBy="purchaseReturnHeaders")
     * @Assert\NotNull()
     */
    private $purchaseInvoiceHeader;
    /**
     * @ORM\OneToMany(targetEntity="PurchaseReturnDetail", mappedBy="purchaseReturnHeader")
     * @Assert\Valid() @Assert\Count(min=1)
     */
    private $purchaseReturnDetails;
    
    public function __construct()
    {
        $this->purchaseReturnDetails = new ArrayCollection();
    }
    
    public function getCodeNumberConstant() { return 'PRT'; }
    
    public function getId() { return $this->id; }
    
    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate(\DateTime $transactionDate = null) { $this->transactionDate = $transactionDate; }
    
    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }

    public function getTotalQuantity() { return $this->totalQuantity; }
    public function setTotalQuantity($totalQuantity) { $this->totalQuantity = $totalQuantity; }

    public function getSubTotal() { return $this->subTotal; }
    public function setSubTotal($subTotal) { $this->subTotal = $subTotal; }

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

    public function getPurchaseInvoiceHeader() { return $this->purchaseInvoiceHeader; }
    public function setPurchaseInvoiceHeader(PurchaseInvoiceHeader $purchaseInvoiceHeader = null) { $this->purchaseInvoiceHeader = $purchaseInvoiceHeader; }

    public function getPurchaseReturnDetails() { return $this->purchaseReturnDetails; }
    public function setPurchaseReturnDetails(Collection $purchaseReturnDetails) { $this->purchaseReturnDetails = $purchaseReturnDetails; }
    
    public function sync()
    {
        $totalQuantity = 0.00;
        $subTotal = 0.00;
        foreach ($this->purchaseReturnDetails as $purchaseReturnDetail) {
            $purchaseReturnDetail->sync();
            $totalQuantity += $purchaseReturnDetail->getQuantity();
            $subTotal += $purchaseReturnDetail->getTotal();
        }
        $this->totalQuantity = $totalQuantity;
        $this->subTotal = $subTotal;
        $this->isTax = 1;
        $this->taxNominal = ($this->isTax ? $this->subTotal * 10 / 100 : 0);
        $this->grandTotal = $this->subTotal + $this->taxNominal + $this->shippingFee;
    }
}
