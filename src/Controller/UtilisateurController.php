<?php

namespace App\Controller;

use App\Entity\Configuration;
use App\Entity\Role;
use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;


#[Route('api/utilisateur', name: 'app_api_utilisateur_')]
final class UtilisateurController extends AbstractController
{
        public function __construct(
            private EntityManagerInterface $manager,
            private UtilisateurRepository $repository,
            private SerializerInterface $serializer,
            private UrlGeneratorInterface $urlGenerator,
        ){

        }

     #[Route(name: 'new', methods:['POST'])]
    public function new(Request $request):JsonResponse
    {

        $utilisateur = $this->serializer->deserialize($request->getContent(), Utilisateur::class, 'json');
        $role = $this->manager->getRepository(Role::class)->findOneBy(['libelle' => 'utilisateur']);

        if (!$role) {
        $role = new Role();
        $role->setLibelle('utilisateur');
        $this->manager->persist($role); 
    }

        $utilisateur->setRole($role);

        // Tell Doctrine you want to (eventually) save the restaurant (no queries yet)
        $this->manager->persist($utilisateur);
        // Actually executes the queries (i.e. the INSERT query)
        $this->manager->flush();
       return new JsonResponse(['message' => 'Utilisateur créé avec succès'], Response::HTTP_CREATED);

    }
    
     #[Route('/{id}', name: 'show', methods: ['GET'])]
     public function show(int $id): JsonResponse
    {
        $utilisateur = $this->repository->findOneBy(['id' => $id]);
        if ($utilisateur) {
            $json = $this->serializer->serialize($utilisateur, 'json', ['groups' => 'utilisateur:read']);
            return new JsonResponse($json, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
        ;
    }

     #[Route('/{id}', name: 'edit', methods: ['PUT'])]
     public function edit(int $id, Request $request): JsonResponse
    {
        $utilisateur = $this->repository->findOneBy(['id' => $id]);
        if ($utilisateur) {
            $utilisateur =$this->serializer->deserialize(
                $request->getContent(),
                Utilisateur::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE=> $utilisateur]
            );
            $role = $utilisateur->getRole();
            if ($role && !$role->getId()) { // nouveau rôle
             $this->manager->persist($role);
            }            
           $this->manager->flush();

           return new JsonResponse(data:null, status:Response::HTTP_NO_CONTENT);
        }
        
        return new JsonResponse(data:null, status:Response::HTTP_NO_FOUND);
    }

     #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
     public function delete(int $id): JsonResponse
    {
        $utilisateur = $this->repository->find($id);
        if ($utilisateur) {
             $this->manager->remove($utilisateur);
             $this->manager->flush();

             return new JsonResponse(null,Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null,Response::HTTP_NOT_FOUND);
       
    }

}