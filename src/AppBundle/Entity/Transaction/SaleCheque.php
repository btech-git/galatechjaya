<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Common\CodeNumberEntity;
use AppBundle\Entity\Transaction\SaleReceiptHeader;
use AppBundle\Entity\Admin\Staff;

/**
 * @ORM\Table(name="transaction_sale_cheque")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\SaleChequeRepository")
 */
class SaleCheque extends CodeNumberEntity
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
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Date()
     */
    private $dateDue;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank()
     */
    private $chequeNumber;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $amount;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $bankName;
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
     * @ORM\ManyToOne(targetEntity="SaleReceiptHeader", inversedBy="saleCheques")
     * @Assert\NotNull()
     */
    private $saleReceiptHeader;
    
    public function __construct()
    {
    }
    
    public function getCodeNumberConstant() { return 'INV'; }
    
    public function getId() { return $this->id; }
    
    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate(\DateTime $transactionDate = null) { $this->transactionDate = $transactionDate; }
    
    public function getDateDue() { return $this->dateDue; }
    public function setDateDue(\DateTime $dateDue = null) { $this->dateDue = $dateDue; }

    public function getChequeNumber() { return $this->chequeNumber; }
    public function setChequeNumber($chequeNumber) { $this->chequeNumber = $chequeNumber; }

    public function getAmount() { return $this->amount; }
    public function setAmount($amount) { $this->amount = $amount; }

    public function getBankName() { return $this->bankName; }
    public function setBankName($bankName) { $this->bankName = $bankName; }

    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }

    public function getStaffFirst() { return $this->staffFirst; }
    public function setStaffFirst(Staff $staffFirst = null) { $this->staffFirst = $staffFirst; }

    public function getStaffLast() { return $this->staffLast; }
    public function setStaffLast(Staff $staffLast = null) { $this->staffLast = $staffLast; }

    public function getSaleReceiptHeader() { return $this->saleReceiptHeader; }
    public function setSaleReceiptHeader(SaleReceiptHeader $saleReceiptHeader = null) { $this->saleReceiptHeader = $saleReceiptHeader; }
}
