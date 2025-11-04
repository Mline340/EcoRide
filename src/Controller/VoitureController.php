<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Repository\VoitureRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/voiture', name: 'app_api_')]
#[OA\Tag(name: 'Voiture')]
class VoitureController extends AbstractController
{
    #[Route('', methods: ['GET'])]
     #[OA\Get(
            path: '/api/voiture',
            summary: 'Afficher les véhicules d\'un utilisateur',
             responses: [
                new OA\Response(
                    response: 200,
                    description: 'Véhicules trouvées avec succès',
                    content: new OA\JsonContent(
                         properties: [
                            new OA\Property(property: 'modele', type: 'string', example: 'Renault Clio'),
                            new OA\Property(property: 'immatriculation', type: 'string', example: 'XX-00-XX'),
                            new OA\Property(property: 'energie', type: 'string', example: 'Electrique'),
                            new OA\Property(property: 'couleur', type: 'string', example: 'Blanche'),
                            new OA\Property(property: 'date_premiere_immatriculation', type: 'string', example: '01/01/2021'),
                ]
            )
        ),
        new OA\Response(
            response: 401,
            description: 'Non authentifié'
        )
    ]
)]

        
    public function getVoiture(VoitureRepository $repo): JsonResponse
    {
        $utilisateur = $this->getUser(); // Récupère l'utilisateur connecté via le token
        if (!$utilisateur) {
            return $this->json(["error" => "Utilisateur non connecté"], 401);
        }

        $preferences = $repo->findOneBy(['Utilisateur' => $utilisateur]);

        // Si pas encore de préférences, on renvoie des valeurs par défaut
        if (!$preferences) {
            return $this->json([
                'modele' => null,
                'immatriculation' => null,
                'energie' => null,
                'couleur' => null,
                'date_premiere_immatriculation' => null
    
            ]);
        }
        return $this->json($preferences, 200, [], [
        'groups' => ['voiture:read']
        ]);
    
    }

    #[Route('', methods: ['PUT'])]
        #[OA\Put(
        path: '/api/voiture',
        summary: 'Modifier les véhicules d\'un utilisateur',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Véhicule de l\'utilisateur à modifier',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'modele', type: 'string', example: 'Renault Clio'),
                            new OA\Property(property: 'immatriculation', type: 'string', example: 'XX-00-XX'),
                            new OA\Property(property: 'energie', type: 'string', example: 'Electrique'),
                            new OA\Property(property: 'couleur', type: 'string', example: 'Blanche'),
                            new OA\Property(property: 'date_premiere_immatriculation', type: 'string', example: '01/01/2021'),
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
                response: 401,
                description: 'Non authentifié'
             )
            ]
            )]

    public function updateVoiture(
        Request $request,
        VoitureRepository $repo,
        EntityManagerInterface $em
    ): JsonResponse {
        
        $utilisateur = $this->getUser();
        if (!$utilisateur) {
            return $this->json(["error" => "Utilisateur non connecté"], 401);
        }

        $data = json_decode($request->getContent(), true);

        $voiture = $repo->findOneBy(['Utilisateur' => $utilisateur]);

        // Si aucune préférence existante → on la crée
        if (!$voiture) {
            $voiture = new Voiture();
            $voiture->setUtilisateur($utilisateur);
        }

        // Mise à jour des champs — ⚠️ Correspond exactement à ton entité
        if (isset($data['modele'])) $voiture->setModele($data['modele']);
        if (isset($data['immatriculation'])) $voiture->setImmatriculation($data['immatriculation']);
        if (isset($data['energie'])) $voiture->setEnergie($data['energie']);
        if (isset($data['couleur'])) $voiture->setCouleur($data['couleur']);
        if (isset($data['date_premiere_immatriculation'])) $voiture->setDatePremiereImmatriculation($data['date_premiere_immatriculation']);
      
        $em->persist($voiture);
        $em->flush();

        return $this->json(['message' => '✅ Véhicules mises à jour avec succès']);
    }
}
