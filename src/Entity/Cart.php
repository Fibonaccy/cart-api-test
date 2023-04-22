<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: "cart", targetEntity: Item::class, cascade: ["persist"], orphanRemoval: true)]
    private Collection $items;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function setItems(Collection $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function addItem(Item $item): self
    {
        $index = $this->findProduct($item->getProduct());
        if ($index < 0) {
            $item->setCart($this);
            $this->items[] = $item;

            return $this;
        }

        $existingItem = $this->getItems()->get($index);
        $existingItem->setQuantity($item->getQuantity());

        return $this;
    }

    public function findProduct(Product $product): int
    {
        /** @var Item $item */
        foreach ($this->getItems() as $key => $item) {
            if ($item->getProduct()->getId() == $product->getId()) {
                return $key;
            }
        }

        return -1;
    }
}
