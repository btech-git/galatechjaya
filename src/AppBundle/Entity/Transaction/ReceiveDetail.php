<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Entity\Common\CodeNumberEntity;
use AppBundle\Entity\Admin\Staff;

/**
 * @ORM\Table(name="transaction_receive_detail")
 * @ORM\Entity
 */
class ReceiveDetail
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
     * @ORM\ManyToOne(targetEntity="ReceiveHeader", inversedBy="receiveDetails")
     * @Assert\NotNull()
     */
    private $receiveHeader;
    /**
     * @ORM\ManyToOne(targetEntity="PurchaseOrderDetail", inversedBy="receiveDetails")
     * @Assert\NotNull()
     */
    private $purchaseOrderDetail;
    /**
     * @ORM\OneToOne(targetEntity="PurchaseInvoiceDetail", mappedBy="receiveDetail")
     */
    private $purchaseInvoiceDetail;
    
    public function __construct()
    {
    }
    
    public function getId() { return $this->id; }

    public function getQuantity() { return $this->quantity; }
    public function setQuantity($quantity) { $this->quantity = $quantity; }

    public function getReceiveHeader() { return $this->receiveHeader; }
    public function setReceiveHeader(ReceiveHeader $receiveHeader = null) { $this->receiveHeader = $receiveHeader; }

    public function getPurchaseOrderDetail() { return $this->purchaseOrderDetail; }
    public function setPurchaseOrderDetail(PurchaseOrderDetail $purchaseOrderDetail = null) { $this->purchaseOrderDetail = $purchaseOrderDetail; }

    public function getPurchaseInvoiceDetail() { return $this->purchaseInvoiceDetail; }
    public function setPurchaseInvoiceDetail(PurchaseInvoiceDetail $purchaseInvoiceDetail = null) { $this->purchaseInvoiceDetail = $purchaseInvoiceDetail; }
}
