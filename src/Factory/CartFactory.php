<?php

namespace App\Factory;

use App\Entity\Cart;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CartFactory
{

    public static function create(Collection $items = new ArrayCollection()): Cart
    {
        $cart = new Cart();
        $cart->setItems($items);
        return $cart;
    }

}
