<?php

namespace AppBundle\Entity\Master;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="master_warehouse") @ORM\Entity
 * @UniqueEntity("code")
 * @UniqueEntity("name")
 */
class Warehouse
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
     * @ORM\Column(type="text")
     * @Assert\NotNull()
     */
    private $address;
    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull() @Assert\Length(min=10, max=20)
     */
    private $phone;
    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotNull()
     */
    private $contactPerson;
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
    
    public function __construct()
    {
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

    public function getAddress() { return $this->address; }
    public function setAddress($address) { $this->address = $address; }

    public function getPhone() { return $this->phone; }
    public function setPhone($phone) { $this->phone = $phone; }

    public function getContactPerson() { return $this->contactPerson; }
    public function setContactPerson($contactPerson) { $this->contactPerson = $contactPerson; }

    public function getNote() { return $this->note; }
    public function setNote($note) { $this->note = $note; }

    public function getIsActive() { return $this->isActive; }
    public function setIsActive($isActive) { $this->isActive = $isActive; }
}
