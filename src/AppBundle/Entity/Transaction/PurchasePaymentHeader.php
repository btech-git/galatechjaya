<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Common\CodeNumberEntity;
use AppBundle\Entity\Master\Supplier;
use AppBundle\Entity\Master\PaymentType;
use AppBundle\Entity\Admin\Staff;

/**
 * @ORM\Table(name="transaction_purchase_payment_header")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\PurchasePaymentHeaderRepository")
 */
class PurchasePaymentHeader extends CodeNumberEntity
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
    private $totalAmount;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\Supplier")
     * @Assert\NotNull()
     */
    private $supplier;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\PaymentType")
     * @Assert\NotNull()
     */
    private $paymentType;
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
     * @ORM\OneToMany(targetEntity="PurchasePaymentDetail", mappedBy="purchasePaymentHeader")
     * @Assert\Valid() @Assert\Count(min=1)
     */
    private $purchasePaymentDetails;
    
    public function __construct()
    {
        $this->purchasePaymentDetails = new ArrayCollection();
    }
    
    public function getCodeNumberConstant() { return 'PPY'; }
    
    public function getId() { return $this->id; }
    
    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate(\DateTime $transactionDate = null) { $this->transactionDate = $transactionDate; }
    
    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }

    public function getTotalAmount() { return $this->totalAmount; }
    public function setTotalAmount($totalAmount) { $this->totalAmount = $totalAmount; }

    public function getSupplier() { return $this->supplier; }
    public function setSupplier(Supplier $supplier = null) { $this->supplier = $supplier; }

    public function getPaymentType() { return $this->paymentType; }
    public function setPaymentType(PaymentType $paymentType = null) { $this->paymentType = $paymentType; }

    public function getStaffFirst() { return $this->staffFirst; }
    public function setStaffFirst(Staff $staffFirst = null) { $this->staffFirst = $staffFirst; }

    public function getStaffLast() { return $this->staffLast; }
    public function setStaffLast(Staff $staffLast = null) { $this->staffLast = $staffLast; }

    public function getPurchasePaymentDetails() { return $this->purchasePaymentDetails; }
    public function setPurchasePaymentDetails(Collection $purchasePaymentDetails) { $this->purchasePaymentDetails = $purchasePaymentDetails; }
}
