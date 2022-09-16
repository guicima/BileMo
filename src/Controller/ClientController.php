<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Security as NelmioSecurity;
use Hateoas\Representation\PaginatedRepresentation;
use Hateoas\Representation\CollectionRepresentation;
use Symfony\Component\Uid\Uuid;

class ClientController extends AbstractController
{
    /**
     * Returns a list of clients
     */
    #[Route('/client', name: 'app_client', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns all user\'s clients',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Client::class, groups: ['client']))
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Clients not found',
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
        description: 'Number of clients per page',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Tag(name: 'Clients')]
    #[NelmioSecurity(name: 'Bearer')]
    public function index(Security $security, SerializerInterface $serializerInterface, Request $request, UserRepository $userRepository): JsonResponse
    {
        try {
            $page = $request->query->get('page') != null ? $request->query->get('page') : 1;
            $limit = $request->query->get('limit') != null ? $request->query->get('limit') : 10;
            $user = $userRepository->findOneBy(['email' => $security->getUser()->getUserIdentifier()]);
            $clients = $user->getClients();
            $pages = ceil(count($clients) / $limit);
            $paginatedCollection = new PaginatedRepresentation(
                new CollectionRepresentation(
                    array_slice($clients->toArray(), ($page - 1) * $limit, $limit),
                    'items'
                ),
                'app_client',
                array(),
                $page,
                $limit,
                $pages,
                'page',
                'limit',
                true,
            );

            if (!$clients) {
                return new JsonResponse(['message' => 'Clients not found'], 404);
            }
            return new JsonResponse($serializerInterface->serialize($paginatedCollection, 'json', SerializationContext::create()->setGroups([
                'Default',
                'items' => [
                    'Default',
                    'client'
                ]
            ])), 200, [], true);
        } catch (\Throwable $th) {
            return new JsonResponse(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Returns a client
     */
    #[Route('/client/{id}', name: 'app_client_show', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns a client',
        content: new OA\JsonContent(
            type: 'object',
            ref: new Model(type: Client::class, groups: ["single_client"])
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Client not found',
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized',
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal server error',
    )]
    #[OA\Tag(name: 'Clients')]
    #[NelmioSecurity(name: 'Bearer')]
    public function show(Uuid $id, Security $security, SerializerInterface $serializerInterface, UserRepository $userRepository): JsonResponse
    {
        try {
            $user = $userRepository->findOneBy(['email' => $security->getUser()->getUserIdentifier()]);
            $client = $user->getClients()->filter(function ($client) use ($id) {
                return $client->getId() == $id;
            })->first();
            if (!$client) {
                return new JsonResponse(['message' => 'Client not found'], 404);
            }
            return new JsonResponse($serializerInterface->serialize($client, "json", null), 200, [], true);
        } catch (\Throwable $th) {
            return new JsonResponse(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Deletes a client
     */
    #[Route('/client/{id}', name: 'app_client_delete', methods: ['DELETE'])]
    #[OA\Response(
        response: 204,
        description: 'Delete a client',
    )]
    #[OA\Response(
        response: 404,
        description: 'Client not found',
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized',
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal server error',
    )]
    #[OA\Tag(name: 'Clients')]
    #[NelmioSecurity(name: 'Bearer')]
    public function delete(Uuid $id, Security $security, ManagerRegistry $doctrine, UserRepository $userRepository): JsonResponse
    {
        try {
            $entityManager = $doctrine->getManager();
            $clientRepository = $entityManager->getRepository(Client::class);

            $user = $userRepository->findOneBy(['email' => $security->getUser()->getUserIdentifier()]);
            $client = $user->getClients()->filter(function ($client) use ($id) {
                return $client->getId() == $id;
            })->first();

            if (!$client) {
                return new JsonResponse(['message' => 'Client not found'], 404);
            }

            $clientRepository->remove($client);
            $entityManager->flush();
            return new JsonResponse(null, 204);
        } catch (\Throwable $th) {
            return new JsonResponse(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Creates a client
     */
    #[Route('/client', name: 'app_client_create', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'Create a client',
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            ref: new Model(type: Client::class, groups: ["client_creation"])
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Create a client',
        content: new OA\JsonContent(
            type: 'object',
            ref: new Model(type: Client::class, groups: ["single_client"])
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad request',
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized',
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal server error',
    )]
    #[OA\Tag(name: 'Clients')]
    #[NelmioSecurity(name: 'Bearer')]
    public function create(Security $security, SerializerInterface $serializerInterface, Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator): JsonResponse
    {
        try {
            $entityManager = $doctrine->getManager();
            $clientRepository = $entityManager->getRepository(Client::class);
            $client = $serializerInterface->deserialize($request->getContent(), Client::class, 'json', DeserializationContext::create()->setGroups(["client_creation"]));
            $client->setUserId($security->getUser());
            $client->setCreatedAt(new \DateTimeImmutable());
            $client->setUpdatedAt(new \DateTimeImmutable());

            $errors = $validator->validate($client);
            if (count($errors) > 0) {
                return new JsonResponse($serializerInterface->serialize($errors, 'json'), 400, [], true);
            }

            $clientRepository->add($client);
            $entityManager->flush();
            return new JsonResponse($serializerInterface->serialize($client, "json", null), 201, [], true);
        } catch (\Throwable $th) {
            return new JsonResponse(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Updates a client
     */
    #[Route('/client/{id}', name: 'app_client_update', methods: ['PUT'])]
    #[OA\RequestBody(
        description: 'Update a client',
        content: new OA\JsonContent(
            type: 'object',
            ref: new Model(type: Client::class, groups: ["client_creation"])
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Update a client',
        content: new OA\JsonContent(
            type: 'object',
            ref: new Model(type: Client::class, groups: ["single_client"])
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad request',
    )]
    #[OA\Response(
        response: 404,
        description: 'Client not found',
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized',
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal server error',
    )]
    #[OA\Tag(name: 'Clients')]
    #[NelmioSecurity(name: 'Bearer')]
    public function update(Uuid $id, Security $security, SerializerInterface $serializerInterface, Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator, UserRepository $userRepository): JsonResponse
    {
        try {
            $entityManager = $doctrine->getManager();
            $user = $userRepository->findOneBy(['email' => $security->getUser()->getUserIdentifier()]);
            $client = $user->getClients()->filter(function ($client) use ($id) {
                return $client->getId() == $id;
            })->first();

            if (!$client) {
                return new JsonResponse(['message' => 'Client not found'], 404);
            }

            $clientUpdate = $serializerInterface->deserialize($request->getContent(), Client::class, 'json', DeserializationContext::create()->setGroups(["client_creation"]));

            $client->setFullName($clientUpdate->getFullName());
            $client->setEmail($clientUpdate->getEmail());
            $client->setUpdatedAt(new \DateTimeImmutable());

            $errors = $validator->validate($client);
            if (count($errors) > 0) {
                return new JsonResponse(['message' => $errors], 400);
            }

            $entityManager->flush();
            return new JsonResponse($serializerInterface->serialize($client, "json", null), 200, [], true);
        } catch (\Throwable $th) {
            return new JsonResponse(['message' => $th->getMessage()], 500);
        }
    }
}
