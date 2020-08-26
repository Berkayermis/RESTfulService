<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Table;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @Table("my_table")
 */
class Order implements JsonSerializable
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected int $id;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="orders")
     */
    private User $user;

    /**
     * @ORM\Column(type="integer")
     */
    private int $productId;


    /**
     * @ORM\Column(type="integer")
     */
    private int $quantity;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $address;

    /**
     * @ORM\Column(type="integer")
     */
    private int $shippingDate;
    
    public function getId(): int
    {
        return $this->id;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function setProductId($productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity($quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress($address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getShippingDate(): int
    {
        return $this->shippingDate;
    }

    public function setShippingDate($shippingDate): self
    {
        $this->shippingDate = $shippingDate;

        return $this;
    }

    public function getUser():?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            "productId" => $this->getProductId(),
            "quantity" => $this->getQuantity(),
            "address" => $this->getAddress(),
            "shippingDate" => $this->getShippingDate(),
            "user"=>$this->getUser()
        ];
    }
}
