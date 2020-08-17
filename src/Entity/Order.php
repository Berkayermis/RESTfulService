<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 */
class Order implements \Serializable
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $orderCode;

    /**
     * @ORM\Column(type="integer")
     */
    private $productId;

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
    private $shippingDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderCode(): ?int
    {
        return $this->orderCode;
    }

    public function setOrderCode(int $orderCode): self
    {
        $this->orderCode = $orderCode;

        return $this;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): self
    {
        $this->productId = $productId;

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
        return $this->shippingDate;
    }

    public function setShippingDate(int $shippingDate): self
    {
        $this->shippingDate = $shippingDate;

        return $this;
    }

    public function takeUser(UserRepository $userRepository,$id){
        $user = $userRepository->find($id);
    }


    public function serialize()
    {
        return serialize([

            $this->id,
            $this->orderCode,
            $this->productId,
            $this->quantity,
            $this->address,
            $this->shippingDate
        ]);
    }

    public function unserialize($string)
    {
        list(
            $this->id,
            $this->orderCode,
            $this->productId,
            $this->quantity,
            $this->address,
            $this->shippingDate
            )=unserialize($string,['allowed_classes'=>false]);
    }

}
