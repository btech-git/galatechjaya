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
 * @ORM\Table(name="transaction_sale_receipt_header")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\SaleReceiptHeaderRepository")
 */
class SaleReceiptHeader extends CodeNumberEntity
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
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $grandTotal;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $totalPayment;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $remaining;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\Customer", inversedBy="saleReceiptHeaders")
     * @Assert\NotNull()
     */
    private $customer;
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
     * @ORM\OneToMany(targetEntity="SaleReceiptDetail", mappedBy="saleReceiptHeader")
     * @Assert\Valid() @Assert\Count(min=1)
     */
    private $saleReceiptDetails;
    /**
     * @ORM\OneToMany(targetEntity="SalePaymentDetail", mappedBy="saleReceiptHeader")
     */
    private $salePaymentDetails;
    /**
     * @ORM\OneToMany(targetEntity="SaleCheque", mappedBy="saleReceiptHeader")
     */
    private $saleCheques;
    
    public function __construct()
    {
        $this->saleReceiptDetails = new ArrayCollection();
        $this->salePaymentDetails = new ArrayCollection();
        $this->saleCheques = new ArrayCollection();
    }
    
    public function getCodeNumberConstant() { return 'STT'; }
    
    public function getId() { return $this->id; }
    
    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate(\DateTime $transactionDate = null) { $this->transactionDate = $transactionDate; }
    
    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }

    public function getGrandTotal() { return $this->grandTotal; }
    public function setGrandTotal($grandTotal) { $this->grandTotal = $grandTotal; }

    public function getTotalPayment() { return $this->totalPayment; }
    public function setTotalPayment($totalPayment) { $this->totalPayment = $totalPayment; }

    public function getRemaining() { return $this->remaining; }
    public function setRemaining($remaining) { $this->remaining = $remaining; }

    public function getCustomer() { return $this->customer; }
    public function setCustomer(Customer $customer = null) { $this->customer = $customer; }

    public function getStaffFirst() { return $this->staffFirst; }
    public function setStaffFirst(Staff $staffFirst = null) { $this->staffFirst = $staffFirst; }

    public function getStaffLast() { return $this->staffLast; }
    public function setStaffLast(Staff $staffLast = null) { $this->staffLast = $staffLast; }

    public function getSaleReceiptDetails() { return $this->saleReceiptDetails; }
    public function setSaleReceiptDetails(Collection $saleReceiptDetails) { $this->saleReceiptDetails = $saleReceiptDetails; }
    
    public function getSalePaymentDetails() { return $this->salePaymentDetails; }
    public function setSalePaymentDetails(Collection $salePaymentDetails) { $this->salePaymentDetails = $salePaymentDetails; }
    
    public function getSaleCheques() { return $this->saleCheques; }
    public function setSaleCheques(Collection $saleCheques) { $this->saleCheques = $saleCheques; }
    
    public function sync()
    {
        $grandTotal = 0.00;
        foreach ($this->saleReceiptDetails as $saleReceiptDetail) {
            $saleReceiptDetail->sync();
            $grandTotal += $saleReceiptDetail->getAmount();
        }
        $this->grandTotal = $grandTotal;
        $this->totalPayment = 0.00;
        $this->remaining = $grandTotal;
    }
}
