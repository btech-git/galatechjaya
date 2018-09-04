<?php

namespace AppBundle\Entity\Transaction;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Master\Product;

/**
 * @ORM\Table(name="transaction_transfer_detail")
 * @ORM\Entity
 */
class TransferDetail
{
    /**
     * @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull()
     */
    private $quantityCurrent;
    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotNull() @Assert\GreaterThan(0)
     */
    private $quantity;
    /**
     * @ORM\ManyToOne(targetEntity="TransferHeader", inversedBy="transferDetails")
     * @Assert\NotNull()
     */
    private $transferHeader;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Master\Product")
     * @Assert\NotNull()
     */
    private $product;
    
    public function __construct()
    {
    }
    
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getQuantityCurrent() { return $this->quantityCurrent; }
    public function setQuantityCurrent($quantityCurrent) { $this->quantityCurrent = $quantityCurrent; }

    public function getQuantity() { return $this->quantity; }
    public function setQuantity($quantity) { $this->quantity = $quantity; }

    public function getTransferHeader() { return $this->transferHeader; }
    public function setTransferHeader(TransferHeader $transferHeader = null) { $this->transferHeader = $transferHeader; }

    public function getProduct() { return $this->product; }
    public function setProduct(Product $product = null) { $this->product = $product; }
}
