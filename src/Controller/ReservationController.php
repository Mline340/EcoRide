<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Utilisateur;
use App\Repository\CovoiturageRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/reservation')]
class ReservationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private CovoiturageRepository $covoiturageRepository,
        private ReservationRepository $reservationRepository
    ) {}

    // Afficher le détail d'un covoiturage
    #[Route('/detail/{id}', name: 'covoiturage_detail', methods: ['GET'])]
    #[OA\Get(
        path: '/api/reservation/detail/{id}',
        summary: 'Afficher le détail d\'un covoiturage',
        security: [],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true,
                description: 'Id du covoiturage', schema: new OA\Schema(type: 'integer'))
        ]
    )]
    public function detail(int $id): JsonResponse
    {
        $covoiturage = $this->covoiturageRepository->find($id);

        if (!$covoiturage) {
            return $this->json(['message' => 'Covoiturage non trouvé'], 404);
        }

        // Calculer les places restantes
        $reservationsValidees = $this->reservationRepository->count([
            'covoiturage' => $covoiturage,
            'statut' => 'validé'
        ]);
        $placesDisponibles = $covoiturage->getNbPlaces() - $reservationsValidees;

        return $this->json([
            'id'                => $covoiturage->getId(),
            'lieu_depart'       => $covoiturage->getLieuDepart(),
            'lieu_arrivee'      => $covoiturage->getLieuArrivee(),
            'date_depart'       => $covoiturage->getDateDepart()->format('d/m/Y'),
            'heure_depart'      => $covoiturage->getHeureDepart()->format('H:i'),
            'prix_personne'     => $covoiturage->getPrixPersonne(),
            'places_disponibles' => $placesDisponibles,
        ]);
    }

    // Participer à un covoiturage
    #[Route('/participer/{id}', name: 'covoiturage_participer', methods: ['POST'])]
    #[OA\Post(
        path: '/api/reservation/participer/{id}',
        summary: 'Participer à un covoiturage',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true,
                description: 'Id du covoiturage', schema: new OA\Schema(type: 'integer'))
        ]
    )]
    public function participer(int $id): JsonResponse
    {
        // 1. Vérifier que l'utilisateur est connecté
       /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();
        if (!$utilisateur) {
            return $this->json([
                'message' => 'Vous devez être connecté pour participer. Créez un compte !',
                'redirect' => '/inscription'
            ], 401);
        }

        // 2. Vérifier que le covoiturage existe
        $covoiturage = $this->covoiturageRepository->find($id);
        if (!$covoiturage) {
            return $this->json(['message' => 'Covoiturage non trouvé'], 404);
        }

        // 3. Vérifier les places disponibles
        $reservationsValidees = $this->reservationRepository->count([
            'covoiturage' => $covoiturage,
            'statut' => 'validé'
        ]);
        $placesDisponibles = $covoiturage->getNbPlaces() - $reservationsValidees;

        if ($placesDisponibles <= 0) {
            return $this->json(['message' => 'Plus de places disponibles'], 400);
        }

        // 4. Vérifier que l'utilisateur a au moins 2 crédits
        if ($utilisateur->getCredit() < 2) {
            return $this->json(['message' => 'Vous n\'avez pas assez de crédits (minimum 2 requis)'], 400);
        }

        // 5. Créer la réservation
        $reservation = new Reservation();
        $reservation->setCovoiturage($covoiturage);
        $reservation->setPassager($utilisateur);
        $reservation->setDateReservation(new \DateTime());
        $reservation->setStatut('validé');

        // 6. Déduire 2 crédits à l'utilisateur
        $utilisateur->setCredit($utilisateur->getCredit() - 2);

        $this->em->persist($reservation);
        $this->em->flush();

        return $this->json([
            'message'         => 'Participation enregistrée avec succès',
            'credits_restants' => $utilisateur->getCredit()
        ], 201);
    }
}