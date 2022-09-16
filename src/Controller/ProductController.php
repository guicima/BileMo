<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
use Hateoas\Representation\PaginatedRepresentation;
use Hateoas\Representation\CollectionRepresentation;

#[Route('/products')]
class ProductController extends AbstractController
{

    /**
     * Returns a list of products
     */
    #[Route(name: 'show_all_products', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns all products',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Product::class, groups: ['product']))
        )
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized',
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal server error',
    )]
    #[OA\Parameter(
        name: 'page',
        in: 'query',
        description: 'Page number',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Parameter(
        name: 'limit',
        in: 'query',
        description: 'Number of products per page',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Tag(name: 'Products')]
    #[Security(name: 'Bearer')]
    public function collection(ProductRepository $productRepository, SerializerInterface $serializerInterface, Request $request): JsonResponse
    {
        try {
            $page = $request->query->get('page') != null ? $request->query->get('page') : 1;
            $limit = $request->query->get('limit') != null ? $request->query->get('limit') : 10;
            $products = $productRepository->findAll();
            $pages = ceil(count($products) / $limit);
            $paginatedCollection = new PaginatedRepresentation(
                new CollectionRepresentation(array_slice($products, ($page - 1) * $limit, $limit), 'items'),
                'show_all_products',
                array(),
                $page,
                $limit,
                $pages,
                'page',
                'limit',
                true,
            );
            return new JsonResponse($serializerInterface->serialize($paginatedCollection, 'json', SerializationContext::create()->setGroups([
                'Default',
                'items' => [
                    'Default',
                    'product'
                ]
            ])), 200, [], true);
        } catch (\Throwable $th) {
            return new JsonResponse(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Returns a product
     */
    #[Route(path: '/{id}', name: 'show_product', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns a product',
        content: new OA\JsonContent(
            type: 'object',
            ref: new Model(type: Product::class, groups: ['single_product'])
        )
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized',
    )]
    #[OA\Response(
        response: 404,
        description: 'Product not found',
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal server error',
    )]
    #[OA\Tag(name: 'Products')]
    #[Security(name: 'Bearer')]
    public function show(int $id, ProductRepository $productRepository, SerializerInterface $serializerInterface): JsonResponse
    {
        try {
            $product = $productRepository->find($id);
            if (!$product) {
                return new JsonResponse(['message' => 'Product not found'], 404);
            }
            return new JsonResponse($serializerInterface->serialize($product, "json", null), 200, [], true);
        } catch (\Throwable $th) {
            return new JsonResponse(['message' => $th->getMessage()], 500);
        }
    }
}
