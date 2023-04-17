<?php

namespace App\Factory;

use App\Entity\Product;
use Doctrine\Common\Collections\Collection;

class ProductFactory
{

    public static function create(string $name, float $price, string $description = null): Product
    {
        $product = new Product();
        $product->setName($name);
        $product->setPrice($price);
        if ($description) {
            $product->setDescription($description);
        }
        return $product;
    }

}
