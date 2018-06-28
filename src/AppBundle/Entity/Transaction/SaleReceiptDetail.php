<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="transaction_sale_receipt_detail") @ORM\Entity
 */
class SaleReceiptDetail
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
     * @ORM\ManyToOne(targetEntity="SaleInvoiceHeader", inversedBy="saleReceiptDetails")
     * @Assert\NotNull()
     */
    private $saleInvoiceHeader;
    /**
     * @ORM\ManyToOne(targetEntity="SaleReceiptHeader", inversedBy="saleReceiptDetails")
     * @Assert\NotNull()
     */
    private $saleReceiptHeader;
    
    public function getId() { return $this->id; }

    public function getAmount() { return $this->amount; }
    public function setAmount($amount) { $this->amount = $amount; }

    public function getMemo() { return $this->memo; }
    public function setMemo($memo) { $this->memo = $memo; }
    
    public function getSaleInvoiceHeader() { return $this->saleInvoiceHeader; }
    public function setSaleInvoiceHeader(SaleInvoiceHeader $saleInvoiceHeader = null) { $this->saleInvoiceHeader = $saleInvoiceHeader; }
    
    public function getSaleReceiptHeader() { return $this->saleReceiptHeader; }
    public function setSaleReceiptHeader(SaleReceiptHeader $saleReceiptHeader = null) { $this->saleReceiptHeader = $saleReceiptHeader; }
    
    public function sync()
    {
        $this->amount = $this->saleInvoiceHeader->getGrandTotal();
    }
}
