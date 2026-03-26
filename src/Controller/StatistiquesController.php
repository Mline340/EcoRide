<?php

namespace App\Controller;

use App\Document\CovoiturageParJour;
use App\Document\CreditParJour;
use App\Document\CreditTotal;
use App\Repository\ReservationRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/statistiques')]
class StatistiqueController extends AbstractController
{
    public function __construct(
        private DocumentManager $dm,
        private ReservationRepository $reservationRepository
    ) {}

    #[Route('/synchroniser', name: 'stat_synchroniser', methods: ['POST'])]
    public function synchroniser(): JsonResponse
    {
        // 1. Récupérer toutes les réservations terminées
        $reservationsTerminees = $this->reservationRepository->findBy(['statut' => 'terminé']);

        // 2. Regrouper par date de départ du covoiturage
        $statsParJour = [];
        foreach ($reservationsTerminees as $reservation) {
            $date = $reservation->getCovoiturage()->getDateDepart()->format('Y-m-d');

            if (!isset($statsParJour[$date])) {
                $statsParJour[$date] = [
                    'covoiturages' => [],
                    'nbPassagers'  => 0,
                ];
            }

            // Compter les covoiturages uniques par jour
            $idCovoit = $reservation->getCovoiturage()->getId();
            $statsParJour[$date]['covoiturages'][$idCovoit] = true;

            // Compter les passagers (1 passager = 2 crédits)
            $statsParJour[$date]['nbPassagers']++;
        }

        // 3. Supprimer les anciens documents MongoDB pour éviter les doublons
        $this->dm->getDocumentCollection(CovoiturageParJour::class)->deleteMany([]);
        $this->dm->getDocumentCollection(CreditParJour::class)->deleteMany([]);
        $this->dm->getDocumentCollection(CreditTotal::class)->deleteMany([]);

        // 4. Insérer les nouvelles statistiques
        $totalCredits = 0;

        foreach ($statsParJour as $date => $data) {
            $nbCovoiturages = count($data['covoiturages']);
            $nbCredits = $data['nbPassagers'] * 2;
            $totalCredits += $nbCredits;

            // Covoiturages par jour
            $statCovoit = new CovoiturageParJour();
            $statCovoit->setDate(new \DateTime($date));
            $statCovoit->setNombreCovoiturages($nbCovoiturages);
            $this->dm->persist($statCovoit);

            // Crédits par jour
            $statCredit = new CreditParJour();
            $statCredit->setDate(new \DateTime($date));
            $statCredit->setNombreCredits($nbCredits);
            $this->dm->persist($statCredit);
        }

        // 5. Total crédits gagnés
        $statTotal = new CreditTotal();
        $statTotal->setDateMiseAJour(new \DateTime());
        $statTotal->setTotalCreditsGagnes($totalCredits);
        $this->dm->persist($statTotal);

        $this->dm->flush();

        return $this->json([
            'message' => 'Synchronisation réussie',
            'jours_traites' => count($statsParJour),
            'total_credits' => $totalCredits,
        ]);
    }
}