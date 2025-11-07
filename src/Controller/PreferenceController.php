<?php

namespace App\Controller;

use App\Entity\Preference;
use App\Entity\Utilisateur;
use App\Repository\PreferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/preference', name: 'app_api_')]
#[OA\Tag(name: 'Préférences')]
class PreferenceController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private PreferenceRepository $repository
    ){ }
    

    #[Route('', methods: ['GET'])]
     #[OA\Get(
            path: '/api/preference',
            summary: 'Afficher les préférences d\'un utilisateur',
             responses: [
                new OA\Response(
                    response: 200,
                    description: 'Préférences trouvées avec succès',
                    content: new OA\JsonContent(
                         properties: [
                            new OA\Property(property: 'passager', type: 'boolean'),
                            new OA\Property(property: 'chauffeur', type: 'boolean'),
                            new OA\Property(property: 'PasChau', type: 'boolean'),
                            new OA\Property(property: 'animaux', type: 'boolean'),
                            new OA\Property(property: 'fumeur', type: 'boolean'),
                            new OA\Property(property: 'NbrPlace', type: 'integer'),
                            new OA\Property(property: 'message', type: 'string'),
                ]
            )
        ),
        new OA\Response(
            response: 401,
            description: 'Non authentifié'
        )
    ]
)]

        
    public function getPreferences(): JsonResponse
    {

        $preferences = $this->repository->findAll();

        // Si pas encore de préférences, on renvoie des valeurs par défaut
        if (!$preferences) {
            return $this->json([
                'passager' => null,
                'chauffeur' => null,
                'PasChau' => null,
                'animaux' => null,
                'fumeur' => null,
                'NbrPlace' => null,
                'message' => null
            ]);
        }

       return $this->json($preferences, 200, [], [
        'groups' => ['preferences:read']
        ]);
    }

    #[Route('/{id}', name: '',  methods: ['GET'])]
     #[OA\Get(
            path: '/api/preference/{id}',
            summary: 'Afficher les préférences d\'un utilisateur',
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
                    description: 'Préférences trouvées avec succès',
                    content: new OA\JsonContent(
                         properties: [
                            new OA\Property(property: 'passager', type: 'boolean'),
                            new OA\Property(property: 'chauffeur', type: 'boolean'),
                            new OA\Property(property: 'PasChau', type: 'boolean'),
                            new OA\Property(property: 'animaux', type: 'boolean'),
                            new OA\Property(property: 'fumeur', type: 'boolean'),
                            new OA\Property(property: 'NbrPlace', type: 'integer'),
                            new OA\Property(property: 'message', type: 'string'),
                ]
            )
        ),
        new OA\Response(
            response: 401,
            description: 'Non authentifié'
        )
    ]
)]

        
    public function getPreferenceById(int $id): JsonResponse
    {
        $utilisateur = $this->manager->getRepository(Utilisateur::class)->findOneBy(['id' => $id]);
        $preferences = $this->repository->findOneBy(['Utilisateur' => $utilisateur]);

        // Si pas encore de préférences, on renvoie des valeurs par défaut
        if (!$preferences) {
            return $this->json([
                'passager' => null,
                'chauffeur' => null,
                'PasChau' => null,
                'animaux' => null,
                'fumeur' => null,
                'NbrPlace' => null,
                'message' => null
            ]);
        }

       return $this->json($preferences, 200, [], [
        'groups' => ['preferences:read']
        ]);
    }

    #[Route('', methods: ['PUT'])]
        #[OA\Put(
        path: '/api/preference',
        summary: 'Modifier les préférences d\'un utilisateur',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Données de l\'utilisateur à modifier',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'passage', type: 'boolean', example: true),
                    new OA\Property(property: 'chauffeur', type: 'boolean', example: true),
                    new OA\Property(property: 'PasChau', type: 'boolean', example: true),
                    new OA\Property(property: 'animaux', type: 'boolean', example: true),
                    new OA\Property(property: 'fumeur', type: 'boolean', example: true),
                    new OA\Property(property: 'NbrPlace', type: 'integer', example: 2),
                    new OA\Property(property: 'message', type: 'string', example: 'blablabla'),
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

    public function updatePreferences(
        Request $request,
        PreferenceRepository $repo,
        EntityManagerInterface $em
    ): JsonResponse {
        
        $utilisateur = $this->getUser();
        if (!$utilisateur) {
            return $this->json(["error" => "Utilisateur non connecté"], 401);
        }

        $data = json_decode($request->getContent(), true);

        $preferences = $repo->findOneBy(['Utilisateur' => $utilisateur]);

        // Si aucune préférence existante → on la crée
        if (!$preferences) {
            $preferences = new Preference();
            $preferences->setUtilisateur($utilisateur);
        }

        // Mise à jour des champs — ⚠️ Correspond exactement à ton entité
        if (isset($data['passager'])) $preferences->setPassager($data['passager']);
        if (isset($data['chauffeur'])) $preferences->setChauffeur($data['chauffeur']);
        if (isset($data['PasChau'])) $preferences->setPasChau($data['PasChau']);
        if (isset($data['animaux'])) $preferences->setAnimaux($data['animaux']);
        if (isset($data['fumeur'])) $preferences->setFumeur($data['fumeur']);
        if (isset($data['NbrPlace'])) $preferences->setNbrPlace($data['NbrPlace']);
        if (isset($data['message'])) $preferences->setMessage($data['message']);

        $em->persist($preferences);
        $em->flush();

        return $this->json(['message' => '✅ Préférences mises à jour avec succès']);
    }


    #[Route('/{id}', methods: ['PUT'])]
    #[OA\Put(
    path: '/api/preference/{id}',
    summary: 'Modifier les préférences d\'un utilisateur',
    security: [['bearerAuth' => []]],
    requestBody: new OA\RequestBody(
        required: true,
        description: 'Données de l\'utilisateur à modifier',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'passage', type: 'boolean', example: true),
                new OA\Property(property: 'chauffeur', type: 'boolean', example: true),
                new OA\Property(property: 'PasChau', type: 'boolean', example: true),
                new OA\Property(property: 'animaux', type: 'boolean', example: true),
                new OA\Property(property: 'fumeur', type: 'boolean', example: true),
                new OA\Property(property: 'NbrPlace', type: 'integer', example: 2),
                new OA\Property(property: 'message', type: 'string', example: 'blablabla'),
            ]
        )
    ),
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

    public function updatePreferenceByUserId(
        int $id,
        Request $request
    ): JsonResponse {
        
        $utilisateur = $this->manager->getRepository(Utilisateur::class)->findOneBy(['id' => $id]);

        $data = json_decode($request->getContent(), true);

        $preferences = $this->repository->findOneBy(['Utilisateur' => $utilisateur]);

        // Si aucune préférence existante → on la crée
        if (!$preferences) {
            $preferences = new Preference();
            $preferences->setUtilisateur($utilisateur);
        }

        // Mise à jour des champs — ⚠️ Correspond exactement à ton entité
        if (isset($data['passager'])) $preferences->setPassager($data['passager']);
        if (isset($data['chauffeur'])) $preferences->setChauffeur($data['chauffeur']);
        if (isset($data['PasChau'])) $preferences->setPasChau($data['PasChau']);
        if (isset($data['animaux'])) $preferences->setAnimaux($data['animaux']);
        if (isset($data['fumeur'])) $preferences->setFumeur($data['fumeur']);
        if (isset($data['NbrPlace'])) $preferences->setNbrPlace($data['NbrPlace']);
        if (isset($data['message'])) $preferences->setMessage($data['message']);

        $this->manager->persist($preferences);
        $this->manager->flush();

        return $this->json(['message' => '✅ Préférences mises à jour avec succès']);
    }
}
