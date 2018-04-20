<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="transaction_sale_return_detail") @ORM\Entity
 */
class SaleReturnDetail
{
    /**
     * @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $quantity;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $unitPrice;
    /**
     * @ORM\Column(type="decimal", precision=18, scale=2)
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $total;
    /**
     * @ORM\ManyToOne(targetEntity="SaleInvoiceDetail", inversedBy="saleReturnDetails")
     * @Assert\NotNull()
     */
    private $saleInvoiceDetail;
    /**
     * @ORM\ManyToOne(targetEntity="SaleReturnHeader", inversedBy="saleReturnDetails")
     * @Assert\NotNull()
     */
    private $saleReturnHeader;
    
    public function getId() { return $this->id; }
    
    public function getQuantity() { return $this->quantity; }
    public function setQuantity($quantity) { $this->quantity = $quantity; }

    public function getUnitPrice() { return $this->unitPrice; }
    public function setUnitPrice($unitPrice) { $this->unitPrice = $unitPrice; }

    public function getTotal() { return $this->total; }
    public function setTotal($total) { $this->total = $total; }

    public function getSaleInvoiceDetail() { return $this->saleInvoiceDetail; }
    public function setSaleInvoiceDetail(SaleInvoiceDetail $saleInvoiceDetail = null) { $this->saleInvoiceDetail = $saleInvoiceDetail; }

    public function getSaleReturnHeader() { return $this->saleReturnHeader; }
    public function setSaleReturnHeader(SaleReturnHeader $saleReturnHeader = null) { $this->saleReturnHeader = $saleReturnHeader; }
    
}
