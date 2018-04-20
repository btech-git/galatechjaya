<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Entity\Common\CodeNumberEntity;
use AppBundle\Entity\Admin\Staff;
use AppBundle\Entity\Transaction\SaleOrderHeader;

/**
 * @ORM\Table(name="transaction_delivery_header")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\DeliveryHeaderRepository")
 */
class DeliveryHeader extends CodeNumberEntity
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
     * @ORM\ManyToOne(targetEntity="SaleOrderHeader", inversedBy="deliveryHeaders")
     * @Assert\NotNull()
     */
    private $saleOrderHeader;
    /**
     * @ORM\OneToOne(targetEntity="SaleInvoiceHeader", mappedBy="deliveryHeader")
     */
    private $saleInvoiceHeader;
    /**
     * @ORM\OneToMany(targetEntity="DeliveryDetail", mappedBy="deliveryHeader")
     * @Assert\Valid() @Assert\Count(min=1)
     */
    private $deliveryDetails;
    
    public function __construct()
    {
        $this->saleOrderDetails = new ArrayCollection();
        $this->deliveryOrders = new ArrayCollection();
    }
    
    public function getCodeNumberConstant() { return 'PO'; }
    
    public function getId() { return $this->id; }
    
    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate($transactionDate) { $this->transactionDate = $transactionDate; }

    public function getReference() { return $this->reference; }
    public function setReference($reference) { $this->reference = $reference; }

    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }

    public function getStaffFirst() { return $this->staffFirst; }
    public function setStaffFirst(Staff $staffFirst = null) { $this->staffFirst = $staffFirst; }

    public function getStaffLast() { return $this->staffLast; }
    public function setStaffLast(Staff $staffLast = null) { $this->staffLast = $staffLast; }

    public function getWarehouse() { return $this->warehouse; }
    public function setWarehouse(Warehouse $warehouse = null) { $this->warehouse = $warehouse; }

    public function getSaleOrderHeader() { return $this->saleOrderHeader; }
    public function setSaleOrderHeader(SaleOrderHeader $saleOrderHeader = null) { $this->saleOrderHeader = $saleOrderHeader; }

    public function getSaleInvoiceHeader() { return $this->saleInvoiceHeader; }
    public function setSaleInvoiceHeader(SaleInvoiceHeader $saleInvoiceHeader = null) { $this->saleInvoiceHeader = $saleInvoiceHeader; }
    
    public function getDeliveryDetails() { return $this->deliveryDetails; }
    public function setDeliveryDetails(Collection $deliveryDetails) { $this->deliveryDetails = $deliveryDetails; }
}
