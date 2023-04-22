<?php

namespace App\Tests\Entity;

use App\Entity\Cart;
use App\Entity\Item;
use App\Entity\Product;
use App\Factory\CartFactory;
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{

    /**
     * @return void
     * @dataProvider addItemProvider
     */
    public function testAddItem(Cart $cart, Item $item): void
    {
        if ($cart->getItems()->count() > 0) {
            $expectedItemCount = $cart->findProduct($item->getProduct()) >= 0 ? 1 : 2;
        } else {
            $expectedItemCount = 1;
        }

        $cart->addItem($item);

        $this->assertTrue($cart->findProduct($item->getProduct()) >= 0);
        $this->assertCount($expectedItemCount, $cart->getItems());
    }

    public function addItemProvider(): array
    {
        $item = new Item();
        $item->setProduct($this->getProduct(1));
        $item->setQuantity(2);
        $otherItem = new Item();
        $otherItem->setProduct($this->getProduct(1));
        $otherItem->setQuantity(1);


        return [
            'first product' => [
                'cart' => CartFactory::create(),
                'item' => $item
            ],
            'same product, different quantity' => [
                'cart' => $this->getCartWithProduct(),
                'item' => $item
            ],
            'second product' => [
                'cart' => $this->getCartWithProduct(),
                'item' => $otherItem
            ]
        ];
    }

    private function getCartWithProduct(): Cart
    {
        $item = new Item();
        $item->setProduct($this->getProduct(1));
        $item->setQuantity(1);
        $cart = CartFactory::create();
        $cart->addItem($item);

        return $cart;
    }

    private function getProduct(int $id): Product
    {
        $product = new Product();
        $product->setId($id);

        return $product;
    }
}
