<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Master\Account;

/**
 * @ORM\Table(name="transaction_deposit_detail") @ORM\Entity
 */
class DepositDetail
{
    /**
     * @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull()
     */
    private $description;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $amount;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\Account")
     * @Assert\NotNull()
     */
    private $account;
    /**
     * @ORM\ManyToOne(targetEntity="DepositHeader", inversedBy="depositDetails")
     * @Assert\NotNull()
     */
    private $depositHeader;
    
    public function getId() { return $this->id; }

    public function getDescription() { return $this->description; }
    public function setDescription($description) { $this->description = $description; }

    public function getAmount() { return $this->amount; }
    public function setAmount($amount) { $this->amount = $amount; }

    public function getAccount() { return $this->account; }
    public function setAccount(Account $account = null) { $this->account = $account; }

    public function getDepositHeader() { return $this->depositHeader; }
    public function setDepositHeader(DepositHeader $depositHeader = null) { $this->depositHeader = $depositHeader; }
}
