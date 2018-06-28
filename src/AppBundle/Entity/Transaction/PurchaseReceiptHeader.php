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
 * @ORM\Table(name="transaction_purchase_receipt_header")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\PurchaseReceiptHeaderRepository")
 */
class PurchaseReceiptHeader extends CodeNumberEntity
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\Supplier", inversedBy="purchaseReceiptHeaders")
     * @Assert\NotNull()
     */
    private $supplier;
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
     * @ORM\OneToMany(targetEntity="PurchaseReceiptDetail", mappedBy="purchaseReceiptHeader")
     * @Assert\Valid() @Assert\Count(min=1)
     */
    private $purchaseReceiptDetails;
    /**
     * @ORM\OneToMany(targetEntity="PurchasePaymentDetail", mappedBy="purchaseReceiptHeader")
     */
    private $purchasePaymentDetails;
    
    public function __construct()
    {
        $this->purchaseReceiptDetails = new ArrayCollection();
        $this->purchasePaymentDetails = new ArrayCollection();
    }
    
    public function getCodeNumberConstant() { return 'PTT'; }
    
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

    public function getSupplier() { return $this->supplier; }
    public function setSupplier(Supplier $supplier = null) { $this->supplier = $supplier; }

    public function getStaffFirst() { return $this->staffFirst; }
    public function setStaffFirst(Staff $staffFirst = null) { $this->staffFirst = $staffFirst; }

    public function getStaffLast() { return $this->staffLast; }
    public function setStaffLast(Staff $staffLast = null) { $this->staffLast = $staffLast; }

    public function getPurchaseReceiptDetails() { return $this->purchaseReceiptDetails; }
    public function setPurchaseReceiptDetails(Collection $purchaseReceiptDetails) { $this->purchaseReceiptDetails = $purchaseReceiptDetails; }
    
    public function getPurchasePaymentDetails() { return $this->purchasePaymentDetails; }
    public function setPurchasePaymentDetails(Collection $purchasePaymentDetails) { $this->purchasePaymentDetails = $purchasePaymentDetails; }
    
    public function sync()
    {
        $grandTotal = 0.00;
        foreach ($this->purchaseReceiptDetails as $purchaseReceiptDetail) {
            $purchaseReceiptDetail->sync();
            $grandTotal += $purchaseReceiptDetail->getAmount();
        }
        $this->grandTotal = $grandTotal;
        $this->totalPayment = 0.00;
        $this->remaining = $grandTotal;
    }
}
