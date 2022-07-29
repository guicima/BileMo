<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/products')]
class ProductController extends AbstractController
{
    #[Route(name: 'show_all_products', methods: ['GET'])]
    public function collection(ProductRepository $productRepository, SerializerInterface $serializerInterface): JsonResponse
    {
        try {
            return new JsonResponse($serializerInterface->serialize($productRepository->findAll(), "json", SerializationContext::create()->setGroups(["product"])), 200, [], true);
        } catch (\Throwable $th) {
            return new JsonResponse(['message' => $th->getMessage()], 500);
        }
    }

    #[Route(path: '/{id}', name: 'show_product', methods: ['GET'])]
    public function show(int $id, ProductRepository $productRepository, SerializerInterface $serializerInterface): JsonResponse
    {
        try {
            $product = $productRepository->find($id);
            if (!$product) {
                return new JsonResponse(['message' => 'Product not found'], 404);
            }
            return new JsonResponse($serializerInterface->serialize($product, "json", SerializationContext::create()->setGroups(["single_product"])), 200, [], true);
        } catch (\Throwable $th) {
            return new JsonResponse(['message' => $th->getMessage()], 500);
        }
    }
}
