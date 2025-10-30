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




#[Route('/api', name: 'app_api_')]
final class SecurityController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager, private SerializerInterface $serializer)
    {
    }
/*Inscription*/
    #[Route('/registration', name: 'registration', methods:['POST'])]
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
        ], Response::HTTP_OK);
    }
}


   