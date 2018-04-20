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
 * @ORM\Table(name="transaction_delivery_detail")
 * @ORM\Entity
 */
class DeliveryDetail
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
     * @ORM\ManyToOne(targetEntity="DeliveryHeader", inversedBy="deliveryDetails")
     * @Assert\NotNull()
     */
    private $deliveryHeader;
    /**
     * @ORM\ManyToOne(targetEntity="SaleOrderDetail", inversedBy="deliveryDetails")
     * @Assert\NotNull()
     */
    private $saleOrderDetail;
    /**
     * @ORM\OneToOne(targetEntity="SaleInvoiceDetail", mappedBy="deliveryDetail")
     */
    private $saleInvoiceDetail;
    
    public function __construct()
    {
    }
    
    public function getId() { return $this->id; }

    public function getQuantity() { return $this->quantity; }
    public function setQuantity($quantity) { $this->quantity = $quantity; }

    public function getDeliveryHeader() { return $this->deliveryHeader; }
    public function setDeliveryHeader(DeliveryHeader $deliveryHeader = null) { $this->deliveryHeader = $deliveryHeader; }

    public function getSaleOrderDetail() { return $this->saleOrderDetail; }
    public function setSaleOrderDetail(SaleOrderDetail $saleOrderDetail = null) { $this->saleOrderDetail = $saleOrderDetail; }

    public function getSaleInvoiceDetail() { return $this->saleInvoiceDetail; }
    public function setSaleInvoiceDetail(SaleInvoiceDetail $saleInvoiceDetail = null) { $this->saleInvoiceDetail = $saleInvoiceDetail; }
}
