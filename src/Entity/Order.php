<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 */
class Order implements \JsonSerializable
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer",unique=true)
     */
    private $order_code;

    /**
     * @ORM\Column(type="integer")
     */
    private $product_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="integer")
     */
    private $shipping_date;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderCode(): ?int
    {
        return $this->order_code;
    }

    public function setOrderCode(int $order_code): self
    {
        $this->order_code = $order_code;

        return $this;
    }

    public function getProductId(): ?int
    {
        return $this->product_id;
    }

    public function setProductId(int $product_id): self
    {
        $this->product_id = $product_id;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getShippingDate(): ?int
    {
        return $this->shipping_date;
    }

    public function setShippingDate(int $shipping_date): self
    {
        $this->shipping_date = $shipping_date;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [

            "order_code" => $this->getOrderCode(),
            "product_id" => $this->getProductId(),
            "quantity" => $this->getQuantity(),
            "address" => $this->getAddress(),
            "shipping_date" => $this->getShippingDate()
        ];
    }
}
