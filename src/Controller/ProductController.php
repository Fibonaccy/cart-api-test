<?php

namespace App\Controller;

use App\Entity\Product;
use App\Factory\ProductFactory;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route(path: "/products", name: "products_")]
class ProductController extends AbstractController
{
    private Serializer $serializer;

    public function __construct(
        private readonly ProductRepository $productRepo
    )
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    #[Route(path: "", name: "all", methods: ["GET"])]
    public function all(): Response
    {
        $products = $this->productRepo->findAll();
        return new Response($this->serializer->serialize($products, "json"));
    }

    #[Route(path: "/{id}", name: "byId", methods:["GET"])]
    public function byId(int $id): Response
    {
        $product = $this->productRepo->findOneBy(["id" => $id]);
        if ($product) {
            return new Response($this->serializer->serialize($product, "json"));
        } else {
            throw new NotFoundHttpException("Product not found by id:" . $id);
        }
    }

    #[Route(path: "", name: "create", methods: ["POST"])]
    public function create(Request $request): Response
    {
        $data = $this->serializer->deserialize($request->getContent(), Product::class, 'json');
        $product = ProductFactory::create($data->getName(), $data->getPrice(), $data->getDescription());
        $this->productRepo->save($product, true);

        return $this->json($this->serializer->serialize($product, "json"), 201);
    }

}
