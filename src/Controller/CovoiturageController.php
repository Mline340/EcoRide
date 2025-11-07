<?php

namespace App\Controller;

use App\Entity\Covoiturage;
use App\Entity\Utilisateur;
use App\Repository\CovoiturageRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/covoiturage', name: 'app_api_')]
#[OA\Tag(name: 'Covoiturage')]
class CovoiturageController extends AbstractController
{
          public function __construct(
        private EntityManagerInterface $manager,
        private CovoiturageRepository $covoiturage
    ){ }
#[Route('', methods: ['POST'])]
        #[OA\Post(
        path: '/api/covoiturage',
        summary: 'Ajout d\'un trajet',
        security: [['bearerAuth' => []]],
        parameters: [
                new OA\Parameter(
                    name: 'id',
                    in: 'path',
                    required: true,
                    description: 'Id de l\'utilisateur à afficher',
                    schema: new OA\Schema(type: 'integer')
                )
            ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Nouveau trajet mit en ligne',
            content: new OA\JsonContent(
                properties: [
                            new OA\Property(property: 'date_depart', type: 'DateTime', example: '03/11/2025'),
                            new OA\Property(property: 'heure_depart', type: 'DateTime', example: '15'),
                            new OA\Property(property: 'lieu_depart', type: 'string', example: 'Montpellier'),
                            new OA\Property(property: 'date_arrivee', type: 'DateTime', example: '03/11/2025'),
                            new OA\Property(property: 'heure_arrivee', type: 'DateTime', example: '17h'),
                            new OA\Property(property: 'lieu_arrivee', type: 'String', example: 'Marseille'),
                            new OA\Property(property: 'Statut', type: 'String', example: 'Pose pour manger le midi'),
                            new OA\Property(property: 'Prix', type: 'float', example: '15'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Trajet ajouté avec succès',
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
    public function updateCovoiturage(int $id, Request $request): JsonResponse
    {
   
        $utilisateur = $this->manager->getRepository(Utilisateur::class)->find($id);
    
        if (!$utilisateur) {
        return new JsonResponse(['error' => 'Utilisateur non trouvé'], 404);
         }

        $data = json_decode($request->getContent(), true);

 
        $covoiturage = $this->repository->findBy(['utilisateur' => $utilisateur]);

    // Création d'un trajet
    $covoiturage = new Voiture();
    $covoiturage->setUtilisateur($utilisateur);
    $covoiturage->setModele($data['modele']);
    $covoiturage->setImmatriculation($data['immatriculation']);
    $covoiturage->setEnergie($data['energie'] ?? null);
    $covoiturage->setCouleur($data['couleur'] ?? null);
   
    if (empty($data['date_premiere_immatriculation'])) {
    return $this->json(["error" => "Le champ 'date_premiere_immatriculation' est obligatoire"], 400);
    }

    $voiture->setDatePremiereImmatriculation($data['date_premiere_immatriculation']);

    

    // Sauvegarde
    $this->manager->persist($voiture);
    $this->manager->flush();

    // Réponse JSON
    return $this->json([
            "message" => "Véhicule ajouté avec succès ✅",
            "voiture" => [
            "id" => $voiture->getId(),
            "modele" => $voiture->getModele(),
            "immatriculation" => $voiture->getImmatriculation(),
            "energie" => $voiture->getEnergie(),
            "couleur" => $voiture->getCouleur(),
            "date_premiere_immatriculation" => $voiture->getDatePremiereImmatriculation(),
        ]
    ], 201);
}

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
    $utilisateur = $this->getUser();

    if (!$utilisateur) {
        return $this->json(["error" => "Utilisateur non connecté"], 401);
    }

    // 🟢 IMPORTANT : la clé doit s’appeler exactement comme la propriété dans l’entité
    $voiture = $repo->findBy(['utilisateur' => $utilisateur]);
    
    return $this->json($voiture, 200, [], [
        'groups' => ['voiture:read']
    ]);

    // ✅ Sinon, retourner les vraies données
    return $this->json([
        'modele' => $voiture->getModele(),
        'immatriculation' => $voiture->getImmatriculation(),
        'energie' => $voiture->getEnergie(),
        'couleur' => $voiture->getCouleur(),
        'datePremiereImmatriculation' => $voiture->getDatePremiereImmatriculation(),
    ], 200);
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

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
        #[OA\Delete(
            path: '/api/voiture/{id}',
            summary: 'Supprimer un véhicule par son ID',
            parameters: [
                new OA\Parameter(
                    name: 'id',
                    in: 'path',
                    required: true,
                    description: 'Id du véhicule à supprimer',
                    schema: new OA\Schema(type: 'integer')
        )
    ],
        responses: [
        new OA\Response(
            response: 204,
            description: 'Véhicule supprimé avec succès'
        ),
        new OA\Response(
            response: 403,
            description: 'Non autorisé - le véhicule ne vous appartient pas'
        ),
        new OA\Response(
            response: 404,
            description: 'Véhicule non trouvé'
        ),
        new OA\Response(
            response: 401,
            description: 'Non authentifié'
        )
    ]
)]
     public function delete(int $id, VoitureRepository $repo, EntityManagerInterface $em): JsonResponse
{
    // ✅ Vérifier que l'utilisateur est connecté
    $utilisateur = $this->getUser();
    if (!$utilisateur) {
        return $this->json(["error" => "Utilisateur non connecté"], 401);
    }

    // ✅ Chercher le véhicule
    $voiture = $repo->find($id);
    
    if (!$voiture) {
        return $this->json(["error" => "Véhicule non trouvé"], 404);
    }

    // ✅ SÉCURITÉ : Vérifier que le véhicule appartient à l'utilisateur
    if ($voiture->getUtilisateur() !== $utilisateur) {
        return $this->json(["error" => "Vous n'êtes pas autorisé à supprimer ce véhicule"], 403);
    }

    // ✅ Supprimer le véhicule
    $em->remove($voiture);
    $em->flush();

    return $this->json(["message" => "Véhicule supprimé avec succès"], 204);
}
}
