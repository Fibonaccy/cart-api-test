<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(name: "product_id", referencedColumnName: "id")]
    private Product $product;

    #[ORM\Column]
    #[Groups(['item'])]
    private int $quantity;

    #[ORM\ManyToOne(targetEntity: "Cart", inversedBy: "items")]
    #[ORM\JoinColumn(name: "cart_id", referencedColumnName: "id")]
    #[Ignore]
    private ?Cart $cart;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

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

    /**
     * @return Cart
     */
    public function getCart(): Cart
    {
        return $this->cart;
    }

    /**
     * @param ?Cart $cart
     */
    public function setCart(?Cart $cart): void
    {
        $this->cart = $cart;
    }
}
