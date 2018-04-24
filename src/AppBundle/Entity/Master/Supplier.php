<?php

namespace AppBundle\Entity\Master;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="master_supplier") @ORM\Entity
 * @UniqueEntity("taxNumber")
 */
class Supplier
{
    /**
     * @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $name;
    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull()
     */
    private $officeAddress;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull()
     */
    private $officeCity;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull()
     */
    private $officeProvince;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull() @Assert\Length(min=5, max=5)
     */
    private $officeZipCode;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull() @Assert\Length(min=10, max=20)
     */
    private $phone;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull() @Assert\Length(min=10, max=20)
     */
    private $fax;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull() @Assert\Length(min=10, max=20)
     */
    private $mobilePhone;
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull()
     */
    private $contactPerson;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull() @Assert\Email()
     */
    private $email;
    /**
     * @ORM\Column(type="string", length=20, unique=true)
     * @Assert\NotBlank() @Assert\Regex("/^\d{2}.\d{3}.\d{3}.\d-\d{3}.\d{3}$/")
     */
    private $taxNumber;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     */
    private $webPage;
    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull()
     */
    private $note;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isActive = true;
    /**
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="supplierPayables")
     * @Assert\NotNull()
     */
    private $accountPayable;
    
    public function __construct()
    {
    }
    
    public function __toString()
    {
        return $this->name;
    }
    
    public function getId() { return $this->id; }

    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }

    public function getOfficeAddress() { return $this->officeAddress; }
    public function setOfficeAddress($officeAddress) { $this->officeAddress = $officeAddress; }

    public function getOfficeCity() { return $this->officeCity; }
    public function setOfficeCity($officeCity) { $this->officeCity = $officeCity; }

    public function getOfficeProvince() { return $this->officeProvince; }
    public function setOfficeProvince($officeProvince) { $this->officeProvince = $officeProvince; }

    public function getOfficeZipCode() { return $this->officeZipCode; }
    public function setOfficeZipCode($officeZipCode) { $this->officeZipCode = $officeZipCode; }

    public function getPhone() { return $this->phone; }
    public function setPhone($phone) { $this->phone = $phone; }

    public function getFax() { return $this->fax; }
    public function setFax($fax) { $this->fax = $fax; }

    public function getMobilePhone() { return $this->mobilePhone; }
    public function setMobilePhone($mobilePhone) { $this->mobilePhone = $mobilePhone; }

    public function getContactPerson() { return $this->contactPerson; }
    public function setContactPerson($contactPerson) { $this->contactPerson = $contactPerson; }

    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }

    public function getTaxNumber() { return $this->taxNumber; }
    public function setTaxNumber($taxNumber) { $this->taxNumber = $taxNumber; }

    public function getWebPage() { return $this->webPage; }
    public function setWebPage($webPage) { $this->webPage = $webPage; }

    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }

    public function getIsActive() { return $this->isActive; }
    public function setIsActive($isActive) { $this->isActive = $isActive; }

    public function getAccountPayable() { return $this->accountPayable; }
    public function setAccountPayable(Account $accountPayable = null) { $this->accountPayable = $accountPayable; }
}
