<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
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

class ClientController extends AbstractController
{
    #[Route('/client', name: 'app_client', methods: ['GET'])]
    public function index(Security $security, SerializerInterface $serializerInterface): JsonResponse
    {
        try {
            $client = $security->getUser()->getClients();
            if (!$client) {
                return new JsonResponse(['message' => 'Clients not found'], 404);
            }
            return new JsonResponse($serializerInterface->serialize($client, "json", SerializationContext::create()->setGroups(["client"])), 200, [], true);
        } catch (\Throwable $th) {
            return new JsonResponse(['message' => $th->getMessage()], 500);
        }
    }

    #[Route('/client/{id}', name: 'app_client_show', methods: ['GET'])]
    public function show(int $id, Security $security, SerializerInterface $serializerInterface): JsonResponse
    {
        try {
            $client = $security->getUser()->getClients()->filter(function ($client) use ($id) {
                return $client->getId() === $id;
            })->first();
            if (!$client) {
                return new JsonResponse(['message' => 'Client not found'], 404);
            }
            return new JsonResponse($serializerInterface->serialize($client, "json", SerializationContext::create()->setGroups(["single_client"])), 200, [], true);
        } catch (\Throwable $th) {
            return new JsonResponse(['message' => $th->getMessage()], 500);
        }
    }

    #[Route('/client/{id}', name: 'app_client_delete', methods: ['DELETE'])]
    public function delete(int $id, Security $security, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $entityManager = $doctrine->getManager();
            $clientRepository = $entityManager->getRepository(Client::class);

            $client = $clientRepository->find($id);

            $hasClient = $security->getUser()->getClients()->contains($client);

            if (!$hasClient) {
                return new JsonResponse(['message' => 'Client not found'], 404);
            }

            $clientRepository->remove($client);
            $entityManager->flush();
            return new JsonResponse(null, 204);
        } catch (\Throwable $th) {
            return new JsonResponse(['message' => $th->getMessage()], 500);
        }
    }

    #[Route('/client', name: 'app_client_create', methods: ['POST'])]
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
            return new JsonResponse($serializerInterface->serialize($client, "json", SerializationContext::create()->setGroups(["single_client"])), 201, [], true);
        } catch (\Throwable $th) {
            return new JsonResponse(['message' => $th->getMessage()], 500);
        }
    }

    #[Route('/client/{id}', name: 'app_client_update', methods: ['PUT'])]
    public function update(int $id, Security $security, SerializerInterface $serializerInterface, Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator): JsonResponse
    {
        try {
            $entityManager = $doctrine->getManager();
            $clientRepository = $entityManager->getRepository(Client::class);
            $client = $clientRepository->find($id);
            $hasClient = $security->getUser()->getClients()->contains($client);
            if (!$hasClient) {
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
            return new JsonResponse($serializerInterface->serialize($client, "json", SerializationContext::create()->setGroups(["single_client"])), 200, [], true);
        } catch (\Throwable $th) {
            return new JsonResponse(['message' => $th->getMessage()], 500);
        }
    }
}