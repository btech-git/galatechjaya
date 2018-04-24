<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Common\CodeNumberEntity;
use AppBundle\Entity\Master\Customer;
use AppBundle\Entity\Admin\Staff;

/**
 * @ORM\Table(name="transaction_sale_invoice_header")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\SaleInvoiceHeaderRepository")
 */
class SaleInvoiceHeader extends CodeNumberEntity
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
    private $customerInvoice;
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
     * @ORM\OneToOne(targetEntity="DeliveryHeader", inversedBy="saleInvoiceHeader")
     * @Assert\NotNull()
     */
    private $deliveryHeader;
    /**
     * @ORM\OneToMany(targetEntity="SaleInvoiceDetail", mappedBy="saleInvoiceHeader")
     * @Assert\Valid() @Assert\Count(min=1)
     */
    private $saleInvoiceDetails;
    /**
     * @ORM\OneToMany(targetEntity="SaleReceiptDetail", mappedBy="saleInvoiceHeader")
     */
    private $saleReceiptDetails;
    /**
     * @ORM\OneToMany(targetEntity="SaleReturnHeader", mappedBy="saleInvoiceHeader")
     * @Assert\Valid() @Assert\Count(min=1)
     */
    private $saleReturnHeaders;
    
    public function __construct()
    {
        $this->saleInvoiceDetails = new ArrayCollection();
        $this->saleReceiptDetails = new ArrayCollection();
    }
    
    public function getCodeNumberConstant() { return 'PIN'; }
    
    public function getId() { return $this->id; }
    
    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate(\DateTime $transactionDate = null) { $this->transactionDate = $transactionDate; }
    
    public function getTaxInvoiceCode() { return $this->taxInvoiceCode; }
    public function setTaxInvoiceCode($taxInvoiceCode) { $this->taxInvoiceCode = $taxInvoiceCode; }

    public function getCustomerInvoice() { return $this->customerInvoice; }
    public function setCustomerInvoice($customerInvoice) { $this->customerInvoice = $customerInvoice; }

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

    public function getDeliveryHeader() { return $this->deliveryHeader; }
    public function setDeliveryHeader(DeliveryHeader $deliveryHeader = null) { $this->deliveryHeader = $deliveryHeader; }

    public function getSaleInvoiceDetails() { return $this->saleInvoiceDetails; }
    public function setSaleInvoiceDetails(Collection $saleInvoiceDetails) { $this->saleInvoiceDetails = $saleInvoiceDetails; }

    public function getSaleReceiptDetails() { return $this->saleReceiptDetails; }
    public function setSaleReceiptDetails(Collection $saleReceiptDetails) { $this->saleReceiptDetails = $saleReceiptDetails; }   

    public function getSaleReturnHeaders() { return $this->saleReturnHeaders; }
    public function setSaleReturnHeaders(Collection $saleReturnHeaders) { $this->saleReturnHeaders = $saleReturnHeaders; }
}
