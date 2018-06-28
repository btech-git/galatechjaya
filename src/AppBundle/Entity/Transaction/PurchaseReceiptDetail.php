<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="transaction_purchase_receipt_detail") @ORM\Entity
 */
class PurchaseReceiptDetail
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
     * @ORM\ManyToOne(targetEntity="PurchaseInvoiceHeader", inversedBy="purchaseReceiptDetails")
     * @Assert\NotNull()
     */
    private $purchaseInvoiceHeader;
    /**
     * @ORM\ManyToOne(targetEntity="PurchaseReceiptHeader", inversedBy="purchaseReceiptDetails")
     * @Assert\NotNull()
     */
    private $purchaseReceiptHeader;
    
    public function getId() { return $this->id; }

    public function getAmount() { return $this->amount; }
    public function setAmount($amount) { $this->amount = $amount; }

    public function getMemo() { return $this->memo; }
    public function setMemo($memo) { $this->memo = $memo; }
    
    public function getPurchaseInvoiceHeader() { return $this->purchaseInvoiceHeader; }
    public function setPurchaseInvoiceHeader(PurchaseInvoiceHeader $purchaseInvoiceHeader = null) { $this->purchaseInvoiceHeader = $purchaseInvoiceHeader; }
    
    public function getPurchaseReceiptHeader() { return $this->purchaseReceiptHeader; }
    public function setPurchaseReceiptHeader(PurchaseReceiptHeader $purchaseReceiptHeader = null) { $this->purchaseReceiptHeader = $purchaseReceiptHeader; }
    
    public function sync()
    {
        $this->amount = $this->purchaseInvoiceHeader->getGrandTotal();
    }
}
