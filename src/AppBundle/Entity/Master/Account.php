<?php

namespace AppBundle\Entity\Master;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="master_account")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Master\AccountRepository")
 * @UniqueEntity("code")
 * @UniqueEntity("name")
 */
class Account
{
    /**
     * @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=20, unique=true)
     * @Assert\NotBlank()
     */
    private $code;
    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\NotBlank()
     */
    private $name;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isCashOrBank = false;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isActive = true;
    /**
     * @ORM\OneToMany(targetEntity="Customer", mappedBy="accountReceivable")
     */
    private $customerReceivables;
    /**
     * @ORM\OneToMany(targetEntity="Customer", mappedBy="accountDownpayment")
     */
    private $customerDownpayments;
    /**
     * @ORM\OneToMany(targetEntity="Supplier", mappedBy="accountPayable")
     */
    private $supplierPayables;
    /**
     * @ORM\ManyToOne(targetEntity="AccountCategory", inversedBy="accounts")
     * @Assert\NotNull()
     */
    private $accountCategory;
    
    public function __construct()
    {
        $this->customerReceivables = new ArrayCollection();
        $this->customerDownpayments = new ArrayCollection();
        $this->supplierPayables = new ArrayCollection();
    }
    
    public function __toString()
    {
        return $this->name;
    }
    
    public function getId() { return $this->id; }

    public function getCode() { return $this->code; }
    public function setCode($code) { $this->code = $code; }

    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }

    public function getIsCashOrBank() { return $this->isCashOrBank; }
    public function setIsCashOrBank($isCashOrBank) { $this->isCashOrBank = $isCashOrBank; }

    public function getIsActive() { return $this->isActive; }
    public function setIsActive($isActive) { $this->isActive = $isActive; }

    public function getCustomerReceivables() { return $this->customerReceivables; }
    public function setCustomerReceivables(Collection $customerReceivables) { $this->customerReceivables = $customerReceivables; }

    public function getCustomerDownpayments() { return $this->customerDownpayments; }
    public function setCustomerDownpayments(Collection $customerDownpayments) { $this->customerDownpayments = $customerDownpayments; }

    public function getSupplierPayables() { return $this->supplierPayables; }
    public function setSupplierPayables(Collection $supplierPayables) { $this->supplierPayables = $supplierPayables; }

    public function getAccountCategory() { return $this->accountCategory; }
    public function setAccountCategory(AccountCategory $accountCategory = null) { $this->accountCategory = $accountCategory; }
}
