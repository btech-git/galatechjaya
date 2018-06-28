<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Common\CodeNumberEntity;
use AppBundle\Entity\Admin\Staff;
use AppBundle\Entity\Master\Warehouse;

/**
 * @ORM\Table(name="transaction_transfer_header")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\TransferHeaderRepository")
 */
class TransferHeader extends CodeNumberEntity
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
    private $warehouseFrom;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\Warehouse")
     * @Assert\NotNull()
     */
    private $warehouseTo;
    /**
     * @ORM\OneToMany(targetEntity="TransferDetail", mappedBy="transferHeader")
     * @Assert\Valid() @Assert\Count(min=1)
     */
    private $transferDetails;
    
    public function __construct()
    {
        $this->transferDetails = new ArrayCollection();
    }
    
    public function getCodeNumberConstant() { return 'TRF'; }
    
    public function getId() { return $this->id; }
    
    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate(\DateTime $transactionDate = null) { $this->transactionDate = $transactionDate; }
    
    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }

    public function getTotalQuantity() { return $this->totalQuantity; }
    public function setTotalQuantity($totalQuantity) { $this->totalQuantity = $totalQuantity; }

    public function getStaffFirst() { return $this->staffFirst; }
    public function setStaffFirst(Staff $staffFirst = null) { $this->staffFirst = $staffFirst; }

    public function getStaffLast() { return $this->staffLast; }
    public function setStaffLast(Staff $staffLast = null) { $this->staffLast = $staffLast; }

    public function getWarehouseFrom() { return $this->warehouseFrom; }
    public function setWarehouseFrom(Warehouse $warehouseFrom = null) { $this->warehouseFrom = $warehouseFrom; }

    public function getWarehouseTo() { return $this->warehouseTo; }
    public function setWarehouseTo(Warehouse $warehouseTo = null) { $this->warehouseTo = $warehouseTo; }

    public function getTransferDetails() { return $this->transferDetails; }
    public function setTransferDetails(Collection $transferDetails) { $this->transferDetails = $transferDetails; }
    
    public function sync()
    {
        $totalQuantity = 0.00;
        foreach ($this->transferDetails as $transferDetail) {
            $totalQuantity += $transferDetail->getQuantity();
        }
        $this->totalQuantity = $totalQuantity;
    }
}
