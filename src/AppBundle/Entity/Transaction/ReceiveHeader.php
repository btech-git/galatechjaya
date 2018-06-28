<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
//use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Entity\Common\CodeNumberEntity;
use AppBundle\Entity\Admin\Staff;
use AppBundle\Entity\Master\Warehouse;
//use AppBundle\Entity\Transaction\PurchaseOrderHeader;

/**
 * @ORM\Table(name="transaction_receive_header")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\ReceiveHeaderRepository")
 */
class ReceiveHeader extends CodeNumberEntity
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
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull()
     */
    private $reference;
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\Warehouse")
     * @Assert\NotNull()
     */
    private $warehouse;
    /**
     * @ORM\ManyToOne(targetEntity="PurchaseOrderHeader", inversedBy="receiveHeaders")
     * @Assert\NotNull()
     */
    private $purchaseOrderHeader;
    /**
     * @ORM\OneToOne(targetEntity="PurchaseInvoiceHeader", mappedBy="receiveHeader")
     */
    private $purchaseInvoiceHeader;
    /**
     * @ORM\OneToMany(targetEntity="ReceiveDetail", mappedBy="receiveHeader")
     * @Assert\Valid() @Assert\Count(min=1)
     */
    private $receiveDetails;
    
    public function __construct()
    {
//        $this->purchaseOrderDetails = new ArrayCollection();
        $this->receiveDetails = new ArrayCollection();
    }
    
    public function getCodeNumberConstant() { return 'RCV'; }
    
    public function getId() { return $this->id; }
    
    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate($transactionDate) { $this->transactionDate = $transactionDate; }

    public function getReference() { return $this->reference; }
    public function setReference($reference) { $this->reference = $reference; }

    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }

    public function getTotalQuantity() { return $this->totalQuantity; }
    public function setTotalQuantity($totalQuantity) { $this->totalQuantity = $totalQuantity; }

    public function getStaffFirst() { return $this->staffFirst; }
    public function setStaffFirst(Staff $staffFirst = null) { $this->staffFirst = $staffFirst; }

    public function getStaffLast() { return $this->staffLast; }
    public function setStaffLast(Staff $staffLast = null) { $this->staffLast = $staffLast; }

    public function getWarehouse() { return $this->warehouse; }
    public function setWarehouse(Warehouse $warehouse = null) { $this->warehouse = $warehouse; }

    public function getPurchaseOrderHeader() { return $this->purchaseOrderHeader; }
    public function setPurchaseOrderHeader(PurchaseOrderHeader $purchaseOrderHeader = null) { $this->purchaseOrderHeader = $purchaseOrderHeader; }

    public function getPurchaseInvoiceHeader() { return $this->purchaseInvoiceHeader; }
    public function setPurchaseInvoiceHeader(PurchaseInvoiceHeader $purchaseInvoiceHeader = null) { $this->purchaseInvoiceHeader = $purchaseInvoiceHeader; }
    
    public function getReceiveDetails() { return $this->receiveDetails; }
    public function setReceiveDetails(Collection $receiveDetails) { $this->receiveDetails = $receiveDetails; }
    
    public function sync()
    {
        $totalQuantity = 0.00;
        foreach ($this->receiveDetails as $receiveDetail) {
            $totalQuantity += $receiveDetail->getQuantity();
        }
        $this->totalQuantity = $totalQuantity;
    }
}
