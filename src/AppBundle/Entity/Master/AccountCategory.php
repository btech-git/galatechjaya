<?php

namespace AppBundle\Entity\Master;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="master_account_category") @ORM\Entity
 * @UniqueEntity("code")
 * @UniqueEntity("name")
 */
class AccountCategory
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
    private $isActive = true;
    /**
     * @ORM\OneToMany(targetEntity="Account", mappedBy="accountCategory")
     */
    private $accounts;
    /**
     * @ORM\OneToMany(targetEntity="AccountCategory", mappedBy="accountCategory")
     */
    private $accountCategories;
    /**
     * @ORM\ManyToOne(targetEntity="AccountCategory", inversedBy="accountCategories")
     */
    private $accountCategory;
    
    public function __construct()
    {
        $this->accounts = new ArrayCollection();
        $this->accountCategories = new ArrayCollection();
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

    public function getIsActive() { return $this->isActive; }
    public function setIsActive($isActive) { $this->isActive = $isActive; }

    public function getAccounts() { return $this->accounts; }
    public function setAccounts(Collection $accounts) { $this->accounts = $accounts; }

    public function getAccountCategories() { return $this->accountCategories; }
    public function setAccountCategories(Collection $accountCategories) { $this->accountCategories = $accountCategories; }

    public function getAccountCategory() { return $this->accountCategory; }
    public function setAccountCategory(AccountCategory $accountCategory = null) { $this->accountCategory = $accountCategory; }
}
