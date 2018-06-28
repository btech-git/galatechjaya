<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Master\Account;

/**
 * @ORM\Table(name="transaction_sale_payment_detail") @ORM\Entity
 */
class SalePaymentDetail
{
    /**
     * @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $amount;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull()
     */
    private $memo;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $totalReceipt;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\Account")
     * @Assert\NotNull()
     */
    private $account;
    /**
     * @ORM\ManyToOne(targetEntity="SaleReceiptHeader", inversedBy="salePaymentDetails")
     * @Assert\NotNull()
     */
    private $saleReceiptHeader;
    /**
     * @ORM\ManyToOne(targetEntity="SalePaymentHeader", inversedBy="salePaymentDetails")
     * @Assert\NotNull()
     */
    private $salePaymentHeader;
    
    public function getId() { return $this->id; }
    
    public function getAmount() { return $this->amount; }
    public function setAmount($amount) { $this->amount = $amount; }

    public function getMemo() { return $this->memo; }
    public function setMemo($memo) { $this->memo = $memo; }

    public function getTotalReceipt() { return $this->totalReceipt; }
    public function setTotalReceipt($totalReceipt) { $this->totalReceipt = $totalReceipt; }

    public function getAccount() { return $this->account; }
    public function setAccount(Account $account = null) { $this->account = $account; }

    public function getSaleReceiptHeader() { return $this->saleReceiptHeader; }
    public function setSaleReceiptHeader(SaleReceiptHeader $saleReceiptHeader = null) { $this->saleReceiptHeader = $saleReceiptHeader; }

    public function getSalePaymentHeader() { return $this->salePaymentHeader; }
    public function setSalePaymentHeader(SalePaymentHeader $salePaymentHeader = null) { $this->salePaymentHeader = $salePaymentHeader; }
    
    public function sync()
    {
        $this->totalReceipt = $this->saleReceiptHeader->getGrandTotal();
    }
}
