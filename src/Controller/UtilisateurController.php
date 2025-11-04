<?php

namespace App\Controller;

use App\Entity\Configuration;
use App\Entity\Role;
use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;


#[Route('/api/utilisateur', name: 'app_api_utilisateur_')]
#[OA\Tag(name: 'Utilisateur')]
final class UtilisateurController extends AbstractController
{
        public function __construct(
            private EntityManagerInterface $manager,
            private UtilisateurRepository $repository,
            private SerializerInterface $serializer,
            private UrlGeneratorInterface $urlGenerator,
        ){ }
        
     #[Route('/new', name: 'new', methods:['POST'])]
       #[OA\Post(
        path: '/api/utilisateur/new',
        summary: 'Inscription d\'un nouvel utilisateur',
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Données de l\'utilisateur à inscrire',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'nom', type: 'string', example: 'Dupont'),
                    new OA\Property(property: 'prenom', type: 'string', example: 'thomas'),
                    new OA\Property(property: 'email', type: 'string', example: 'thomas@email.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'DupThom'),
                    new OA\Property(property: 'telephone', type: 'string', example: '456789'),
                    new OA\Property(property: 'ville', type: 'string', example: 'assas'),
                    new OA\Property(property: 'date_naissance', type: 'string', example: '02/08/1996'),
                    new OA\Property(property: 'photo', type: 'string', example: 'toto'),
                    new OA\Property(property: 'pseudo', type: 'string', example: 'ThoDup'),
                    new OA\Property(property: 'code_postal', type: 'string', example: '34820'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Utilisateur inscrit avec succès',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'userutilisateur', type: 'string', example: 'Nom d\'utilisateur'),
                        new OA\Property(property: 'apiToken', type: 'string', example: '31a023e212f116124a36af14ea0c1c3806eb9378'),
                        new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string', example: 'ROLE_USER'))
                    ]
                )
            )
        ]
    )]
    
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
         #[OA\Get(
            path: '/api/utilisateur/{id}',
            summary: 'Afficher un utilisateur par son ID',
            parameters: [
                new OA\Parameter(
                    name: 'id',
                    in: 'path',
                    required: true,
                    description: 'Id de l\'utilisateur à afficher',
                    schema: new OA\Schema(type: 'integer')
        )
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Utilisateur trouvé avec succès',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'userutilisateur', type: 'string', example: 'Nom d\'utilisateur'),
                    new OA\Property(property: 'apiToken', type: 'string', example: '31a023e212f116124a36af14ea0c1c3806eb9378'),
                    new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string', example: 'ROLE_USER'))
                ]
            )
        ),
        new OA\Response(
            response: 404,
            description: 'Utilisateur non trouvé'
        )
    ]
    )]

        
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
        #[OA\Put(
        path: '/api/utilisateur/{id}',
        summary: 'Modifier un utilisateur par son ID',
         parameters: [
                new OA\Parameter(
                    name: 'id',
                    in: 'path',
                    required: true,
                    description: 'Id de l\'utilisateur à modifier',
                    schema: new OA\Schema(type: 'integer')
        )
    ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Données de l\'utilisateur à modifier',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'telephone', type: 'string', example: '456789'),
                    new OA\Property(property: 'ville', type: 'string', example: 'assas'),
                    new OA\Property(property: 'photo', type: 'string', example: 'toto'),
                    new OA\Property(property: 'code_postal', type: 'string', example: '34820'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Utilisateur modifié avec succès',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'userutilisateur', type: 'string', example: 'Nom d\'utilisateur'),
                        new OA\Property(property: 'apiToken', type: 'string', example: '31a023e212f116124a36af14ea0c1c3806eb9378'),
                        new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string', example: 'ROLE_USER'))
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Utilisateur non trouvé'
            )
         ]
    )]
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
        #[OA\Delete(
            path: '/api/utilisateur/{id}',
            summary: 'Supprimer un utilisateur par son ID',
            parameters: [
                new OA\Parameter(
                    name: 'id',
                    in: 'path',
                    required: true,
                    description: 'Id de l\'utilisateur à supprimer',
                    schema: new OA\Schema(type: 'integer')
        )
    ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Utilisateur supprimé avec succès'
        ),
            new OA\Response(
                response: 404,
                description: 'Utilisateur non trouvé'
        )
    ]
)]
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