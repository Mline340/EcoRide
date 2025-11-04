<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Utilisateur;
use App\Entity\Role;
use App\Entity\Configuration;
use OpenApi\Attributes as OA;




#[Route('/api', name: 'app_api_')]
#[OA\Tag(name: 'Security')]
final class SecurityController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager, private SerializerInterface $serializer)
    {
    }
/*Inscription*/
    #[Route('/registration', name: 'registration', methods:['POST'])]
    #[OA\Post(
        path: '/api/registration',
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
                        new OA\Property(property: 'id', type: 'integer', example: '3'),
                        new OA\Property(property: 'apiToken', type: 'string', example: '31a023e212f116124a36af14ea0c1c3806eb9378'),
                        new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string', example: 'ROLE_USER'))
                    ]
                )
            )
        ]
    )]
    public function new(Request $request, UserPasswordHasherInterface $passwordHasher):JsonResponse
    {
        $utilisateur = $this->serializer->deserialize($request->getContent(), Utilisateur::class, 'json');
        
        $utilisateur-> setPassword($passwordHasher->hashPassword($utilisateur, $utilisateur->getPassword()));
        $role = $this->manager->getRepository(Role::class)->findOneBy(['libelle' => 'utilisateur']);

        if (!$role) {
        $role = new Role();
        $role->setLibelle('utilisateur');
        $this->manager->persist($role); 
    }

        $utilisateur->setRole($role);

        $this->manager->persist($utilisateur);
        $this->manager->flush();
        return new JsonResponse(
            ['Email'  => $utilisateur->getEmail(), 'apiToken' => $utilisateur->getApiToken(), 'pseudo' => $utilisateur->getPseudo()],
            Response::HTTP_CREATED
        );
    }


/*Connexion*/
   #[Route('/login', name: 'login', methods: 'POST')]
   #[OA\Post(
    path: '/api/login',
    summary: 'Connecter un utilisateur',
    requestBody: new OA\RequestBody(
        required: true,
        description: 'Données de l\'utilisateur pour se connecter',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'email', type: 'string', example: 'adresse@email.com'),
                new OA\Property(property: 'password', type: 'string', example: 'Mot de passe')
            ]
        )
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Connexion réussie',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'user', type: 'string', example: 'Nom d\'utilisateur'),
                    new OA\Property(property: 'id', type: 'integer', example: '3'),
                    new OA\Property(property: 'apiToken', type: 'string', example: '31a023e212f116124a36af14ea0c1c3806eb9378'),
                    new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string', example: 'ROLE_USER'))
                ]
            )
        )
    ]
)]
    public function login(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return new JsonResponse(['error' => 'Email and password required'], Response::HTTP_BAD_REQUEST);
        }

        $user = $em->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);

        if (!$user) {
            return new JsonResponse(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'email' => $user->getEmail(),
            'apiToken' => $user->getApiToken(),
            'id'=>$user->getId(),
        ], Response::HTTP_OK);
    }
}


   