<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Item;
use App\Factory\CartFactory;
use App\Repository\CartRepository;
use App\Repository\ItemRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

#[Route(path: "/carts", name: "carts_")]
class CartController extends AbstractController
{

    public function __construct(
        private readonly CartRepository    $cartRepo,
        private readonly ProductRepository $productRepo,
        private readonly ItemRepository    $itemRepo
    )
    {
    }

    #[Route(path: "", name: "all", methods: ["GET"])]
    public function all(): Response
    {
        $carts = $this->cartRepo->findAll();
        return $this->json($carts, 201, [], [ObjectNormalizer::IGNORED_ATTRIBUTES => ['cart']]);
    }

    #[Route(path: "/{id}", name: "byId", methods:["GET"])]
    public function byId(int $id): Response
    {
        $cart = $this->cartRepo->findOneBy(["id" => $id]);
        if ($cart) {
            return $this->json($cart, 200, [], [ObjectNormalizer::IGNORED_ATTRIBUTES => ['cart']]);
        } else {
            throw new NotFoundHttpException("Cart not found by id:" . $id);
        }
    }

    #[Route(path: "", name: "create", methods: ["POST"])]
    public function create(Request $request): Response
    {
        $cart = CartFactory::create();
        $this->cartRepo->save($cart, true);

        return $this->json($cart, 201, [], []);
    }

    #[Route(path: "/{id}", name: "addProduct", methods: ["POST"])]
    public function addProduct(int $id, Request $request): Response
    {
        $cart = $this->cartRepo->findOneBy(["id" => $id]);
        if (!$cart) {
            throw new NotFoundHttpException("Cart not found by id:" . $id);
        }

        // TODO: refactor this to some service

        $data = json_decode($request->getContent(), true);
        $productId = $data['product_id'] ?? null;
        $quantity = $data['quantity'] ?? null;
        $product = $this->validateProductInput($productId, $quantity);

        $item = new Item();
        $item->setProduct($product);
        $item->setQuantity($quantity);

        $cart->addItem($item);

        $this->cartRepo->save($cart, true);

        return $this->json($cart, 201, [], [AbstractNormalizer::IGNORED_ATTRIBUTES => ['cart']]);
    }

    #[Route(path: "/{id}/items/{itemId}", name: "removeCartItem", methods: ["DELETE"])]
    public function removeItem(Cart $cart, int $itemId): Response
    {
        $item = $this->itemRepo->find($itemId);

        if (!$item) {
            return $this->json(['message' => 'Item not found'], Response::HTTP_NOT_FOUND);
        }

        if ($item->getCart() !== $cart) {
            return $this->json(['message' => 'Item does not belong to the given cart'], Response::HTTP_BAD_REQUEST);
        }

        $this->itemRepo->remove($item, true);

        return $this->json(['message' => 'Item removed from cart'], Response::HTTP_OK);
    }

    /**
     * @param Cart|null $cart
     * @param int $id
     * @param mixed $productId
     * @param mixed $quantity
     * @return \App\Entity\Product
     */
    public function validateProductInput(mixed $productId, mixed $quantity): \App\Entity\Product
    {
        if (empty($productId) || !is_int($quantity) || !is_int($productId)) {
            throw new BadRequestHttpException("product_id and quantity of type int required");
        }

        if ($quantity < 1) {
            throw new BadRequestHttpException("quantity should not be less than 1");
        }

        $product = $this->productRepo->findOneBy(["id" => $productId]);
        if (!$product) {
            throw new NotFoundHttpException("Product not found by id:" . $productId);
        }
        return $product;
    }
}
