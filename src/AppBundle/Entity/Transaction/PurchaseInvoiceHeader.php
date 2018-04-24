<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Common\CodeNumberEntity;
use AppBundle\Entity\Master\Supplier;
use AppBundle\Entity\Admin\Staff;

/**
 * @ORM\Table(name="transaction_purchase_invoice_header")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\PurchaseInvoiceHeaderRepository")
 */
class PurchaseInvoiceHeader extends CodeNumberEntity
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
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull()
     */
    private $taxInvoiceCode;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull()
     */
    private $supplierInvoice;
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
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $totalReturn;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $grandTotal;
    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull()
     */
    private $note;
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
     * @ORM\OneToOne(targetEntity="ReceiveHeader", inversedBy="purchaseInvoiceHeader")
     * @Assert\NotNull()
     */
    private $receiveHeader;
    /**
     * @ORM\OneToMany(targetEntity="PurchaseInvoiceDetail", mappedBy="purchaseInvoiceHeader")
     * @Assert\Valid() @Assert\Count(min=1)
     */
    private $purchaseInvoiceDetails;
    /**
     * @ORM\OneToMany(targetEntity="PurchaseReceiptDetail", mappedBy="purchaseInvoiceHeader")
     */
    private $purchaseReceiptDetails;
    /**
     * @ORM\OneToMany(targetEntity="PurchaseReturnHeader", mappedBy="purchaseInvoiceHeader")
     * @Assert\Valid() @Assert\Count(min=1)
     */
    private $purchaseReturnHeaders;
    
    public function __construct()
    {
        $this->purchaseInvoiceDetails = new ArrayCollection();
        $this->purchaseReceiptDetails = new ArrayCollection();
    }
    
    public function getCodeNumberConstant() { return 'PIN'; }
    
    public function getId() { return $this->id; }
    
    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate(\DateTime $transactionDate = null) { $this->transactionDate = $transactionDate; }
    
    public function getTaxInvoiceCode() { return $this->taxInvoiceCode; }
    public function setTaxInvoiceCode($taxInvoiceCode) { $this->taxInvoiceCode = $taxInvoiceCode; }

    public function getSupplierInvoice() { return $this->supplierInvoice; }
    public function setSupplierInvoice($supplierInvoice) { $this->supplierInvoice = $supplierInvoice; }

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

    public function getTotalReturn() { return $this->totalReturn; }
    public function setTotalReturn($totalReturn) { $this->totalReturn = $totalReturn; }

    public function getGrandTotal() { return $this->grandTotal; }
    public function setGrandTotal($grandTotal) { $this->grandTotal = $grandTotal; }

    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }

    public function getIsTax() { return $this->isTax; }
    public function setIsTax($isTax) { $this->isTax = $isTax; }

    public function getStaffFirst() { return $this->staffFirst; }
    public function setStaffFirst(Staff $staffFirst = null) { $this->staffFirst = $staffFirst; }

    public function getStaffLast() { return $this->staffLast; }
    public function setStaffLast(Staff $staffLast = null) { $this->staffLast = $staffLast; }

    public function getReceiveHeader() { return $this->receiveHeader; }
    public function setReceiveHeader(ReceiveHeader $receiveHeader = null) { $this->receiveHeader = $receiveHeader; }

    public function getPurchaseInvoiceDetails() { return $this->purchaseInvoiceDetails; }
    public function setPurchaseInvoiceDetails(Collection $purchaseInvoiceDetails) { $this->purchaseInvoiceDetails = $purchaseInvoiceDetails; }

    public function getPurchaseReceiptDetails() { return $this->purchaseReceiptDetails; }
    public function setPurchaseReceiptDetails(Collection $purchaseReceiptDetails) { $this->purchaseReceiptDetails = $purchaseReceiptDetails; }   

    public function getPurchaseReturnHeaders() { return $this->purchaseReturnHeaders; }
    public function setPurchaseReturnHeaders(Collection $purchaseReturnHeaders) { $this->purchaseReturnHeaders = $purchaseReturnHeaders; }
}
