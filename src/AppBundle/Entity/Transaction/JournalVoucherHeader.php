<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Common\CodeNumberEntity;
use AppBundle\Entity\Admin\Staff;

/**
 * @ORM\Table(name="transaction_journal_voucher_header")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Transaction\JournalVoucherHeaderRepository")
 */
class JournalVoucherHeader extends CodeNumberEntity
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
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $totalDebit;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThanOrEqual(0)
     */
    private $totalCredit;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Admin\Staff")
     * @Assert\NotNull()
     */
    private $staff;
    /**
     * @ORM\OneToMany(targetEntity="JournalVoucherDetail", mappedBy="journalVoucherHeader")
     * @Assert\Valid() @Assert\Count(min=1)
     */
    private $journalVoucherDetails;
    
    public function __construct()
    {
        $this->journalVoucherDetails = new ArrayCollection();
    }
    
    public function getCodeNumberConstant() { return 'JVC'; }
    
    public function getId() { return $this->id; }
    
    public function getTransactionDate() { return $this->transactionDate; }
    public function setTransactionDate(\DateTime $transactionDate = null) { $this->transactionDate = $transactionDate; }
    
    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }
    
    public function getTotalDebit() { return $this->totalDebit; }
    public function setTotalDebit($totalDebit) { $this->totalDebit = $totalDebit; }

    public function getTotalCredit() { return $this->totalCredit; }
    public function setTotalCredit($totalCredit) { $this->totalCredit = $totalCredit; }

    public function getStaff() { return $this->staff; }
    public function setStaff(Staff $staff = null) { $this->staff = $staff; }
    
    public function getJournalVoucherDetails() { return $this->journalVoucherDetails; }
    public function setJournalVoucherDetails(Collection $journalVoucherDetails) { $this->journalVoucherDetails = $journalVoucherDetails; }
    
    public function sync()
    {
        $totalDebit = 0.00;
        $totalCredit = 0.00;
        foreach ($this->journalVoucherDetails as $journalVoucherDetail) {
            $totalDebit += $journalVoucherDetail->getDebit();
            $totalCredit += $journalVoucherDetail->getCredit();
        }
        $this->totalDebit = $totalDebit;
        $this->totalCredit = $totalCredit;
    }
}
