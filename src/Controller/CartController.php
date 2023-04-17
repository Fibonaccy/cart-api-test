<?php

namespace App\Controller;

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
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route(path: "/carts", name: "carts_")]
class CartController extends AbstractController
{

    private Serializer $serializer;

    public function __construct(
        private CartRepository $cartRepo,
        private ProductRepository $productRepo,
        private ItemRepository $itemRepo

    )
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    #[Route(path: "", name: "all", methods: ["GET"])]
    public function all(): Response
    {
        $carts = $this->cartRepo->findAll();
        return new Response($this->serializer->serialize($carts, "json"));
    }

    #[Route(path: "/{id}", name: "byId", methods:["GET"])]
    public function byId(int $id): Response
    {
        $cart = $this->cartRepo->findOneBy(["id" => $id]);
        if ($cart) {
            return new Response($this->serializer->serialize($cart, "json"));
        } else {
            throw new NotFoundHttpException("Cart not found by id:" . $id);
        }
    }

    #[Route(path: "", name: "create", methods: ["POST"])]
    public function create(Request $request): Response
    {
        $cart = CartFactory::create();
        $this->cartRepo->save($cart, true);

        return $this->json($this->serializer->serialize($cart, "json"), 201);
    }

    #[Route(path: "/{id}", name: "addProduct", methods: ["POST"])]
    public function addProduct(int $id, Request $request): Response
    {
        $cart = $this->cartRepo->findOneBy(["id" => $id]);
        // TODO: fix and refactor these entities



        return new Response($this->serializer->serialize($cart, "json"));

    }

    // TODO: add PUT endpoint or some way to edit a products quantity and remove on 0
}
