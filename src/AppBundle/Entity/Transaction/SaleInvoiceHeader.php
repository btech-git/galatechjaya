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
    private $customerOrderNumber;
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
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $totalReturn;
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\Customer")
     * @Assert\NotNull()
     */
    private $customer;
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
     */
    private $saleReturnHeaders;
    
    public function __construct()
    {
        $this->saleInvoiceDetails = new ArrayCollection();
        $this->saleReceiptDetails = new ArrayCollection();
        $this->saleReturnHeaders = new ArrayCollection();
    }
    
    public function getCodeNumberConstant() { return 'SIN'; }
    
    public function getId() { return $this->id; }
    
    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate(\DateTime $transactionDate = null) { $this->transactionDate = $transactionDate; }
    
    public function getTaxInvoiceCode() { return $this->taxInvoiceCode; }
    public function setTaxInvoiceCode($taxInvoiceCode) { $this->taxInvoiceCode = $taxInvoiceCode; }

    public function getCustomerOrderNumber() { return $this->customerOrderNumber; }
    public function setCustomerOrderNumber($customerOrderNumber) { $this->customerOrderNumber = $customerOrderNumber; }

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

    public function getTotalReturn() { return $this->totalReturn; }
    public function setTotalReturn($totalReturn) { $this->totalReturn = $totalReturn; }

    public function getGrandTotal() { return $this->grandTotal; }
    public function setGrandTotal($grandTotal) { $this->grandTotal = $grandTotal; }

    public function getIsTax() { return $this->isTax; }
    public function setIsTax($isTax) { $this->isTax = $isTax; }

    public function getStaffFirst() { return $this->staffFirst; }
    public function setStaffFirst(Staff $staffFirst = null) { $this->staffFirst = $staffFirst; }

    public function getStaffLast() { return $this->staffLast; }
    public function setStaffLast(Staff $staffLast = null) { $this->staffLast = $staffLast; }

    public function getCustomer() { return $this->customer; }
    public function setCustomer(Customer $customer = null) { $this->customer = $customer; }

    public function getSaleInvoiceDetails() { return $this->saleInvoiceDetails; }
    public function setSaleInvoiceDetails(Collection $saleInvoiceDetails) { $this->saleInvoiceDetails = $saleInvoiceDetails; }

    public function getSaleReceiptDetails() { return $this->saleReceiptDetails; }
    public function setSaleReceiptDetails(Collection $saleReceiptDetails) { $this->saleReceiptDetails = $saleReceiptDetails; }   

    public function getSaleReturnHeaders() { return $this->saleReturnHeaders; }
    public function setSaleReturnHeaders(Collection $saleReturnHeaders) { $this->saleReturnHeaders = $saleReturnHeaders; }
    
    public function sync()
    {
        $totalQuantity = 0.00;
        $subTotal = 0.00;
        foreach ($this->saleInvoiceDetails as $saleInvoiceDetail) {
            $saleInvoiceDetail->sync();
            $totalQuantity += $saleInvoiceDetail->getQuantity();
            $subTotal += $saleInvoiceDetail->getTotal();
        }
        $this->totalQuantity = $totalQuantity;
        $this->subTotal = $subTotal;
        $this->discountNominal = $this->subTotal * $this->discountPercentage / 100;
        $this->taxNominal = ($this->isTax ? ($this->subTotal - $this->discountNominal) * 10 / 100 : 0);
        $this->grandTotal = $this->subTotal - $this->discountNominal + $this->taxNominal + $this->shippingFee;
        $this->totalReturn = 0.00;
    }
    
    public function getAveragePurchaseGrandTotal()
    {
        $total = 0.00;
        foreach ($this->saleInvoiceDetails as $saleInvoiceDetail) {
            $total += $saleInvoiceDetail->getAveragePurchaseTotal();
        }
        
        return $total;
    }
    
    public function getProfitLoss()
    {
        return $this->grandTotal - $this->getAveragePurchaseGrandTotal();
    }
    
}
