<?php

namespace App\Controller\admin;

use App\Repository\InvitationRepository;
use App\Repository\LightRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/admin/tableau-de-bord', name: 'admin.')]
class AdminController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private MessageRepository $messageRepository,
        private LightRepository $lightRepository,
        private InvitationRepository $invitationRepository,
    ) {
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/', name: 'index')]
    public function index(ChartBuilderInterface $chartBuilder): Response
    {
        $this->denyAccessUnlessGranted(attribute: 'ROLE_ADMIN');

        $totalUsers = $this->userRepository->countUsers();
        $invitationsAccepted = $this->invitationRepository->countInvitationsAccepted();
        $invitationsNotAccepted = $this->invitationRepository->countInvitationsNotAccepted();
        $totalMessages = $this->messageRepository->countMessages();
        $messagesByMonth = $this->messageRepository->countMessagesByMonth();
        $totalLights = $this->lightRepository->countLights();

        // Récupérer les données des messages par mois
        $messagesByMonth = $this->messageRepository->countMessagesByMonth();

        // Traduire les noms des mois en français
        $monthNamesFrench = [
            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre',
        ];

        // Utiliser array_combine pour associer les données des messages avec les noms des mois en français
        $messagesByMonth = array_combine($monthNamesFrench, $messagesByMonth);

        // Initialiser un tableau vide pour stocker les données des trois derniers mois précédant le mois actuel
        $data = [];

        // Obtenir le numéro du mois actuel (1 pour janvier, 2 pour février, etc.)
        $currentMonth = (int) date(format: 'n');

        // Parcourir les quatre derniers mois, y compris le mois actuel
        for ($i = 0; $i < 4; ++$i) {
            // Calculer le mois en soustrayant $i de la valeur du mois actuel
            $month = $currentMonth - $i;

            // Vérifier si le mois est inférieur à 1 (nous sommes en janvier)
            if ($month < 1) {
                // Si nous sommes en janvier, ajuster le mois à décembre de l'année précédente
                $month += 12;
            }

            // Obtenir le nom du mois en français
            $monthName = $monthNamesFrench[$month - 1];

            // Vérifier si le mois existe dans les données des messages
            if (isset($messagesByMonth[$monthName])) {
                // Ajouter les données du mois au tableau $data
                $data[$monthName] = $messagesByMonth[$monthName];
            } else {
                // Si les données du mois n'existent pas, initialiser à 0
                $data[$monthName] = 0;
            }
        }

        // Inverser l'ordre des données pour que le graphique commence par le mois le plus récent
        // et se termine par le mois le plus ancien
        $data = array_reverse(array: $data, preserve_keys: true);

        // Tableau de couleur pour tester chartJs
        $colors = [
            'rouge' => 'rgb(255, 99, 132)',
            'vert' => 'rgb(0, 210, 100)',
            'bleu' => 'rgb(54, 162, 235)',
            'orange' => 'rgb(255, 159, 64)',
            'violet' => 'rgb(153, 102, 255)',
            'jaune' => 'rgb(255, 205, 86)',
            'gris' => 'rgb(201, 203, 207)',
            'rose' => 'rgb(255, 192, 203)',
            'marron' => 'rgb(210, 105, 30)',
            'cyan' => 'rgb(0, 255, 255)',
            'noir' => 'rgb(0, 0, 0)',
            'blanc' => 'rgb(255, 255, 255)',
            'turquoise' => 'rgb(64, 224, 208)',
        ];

        $chart = $chartBuilder->createChart(type: Chart::TYPE_LINE);
        $chart->setData([
            'datasets' => [
                [
                    'label' => 'Nombre de messages envoyés',
                    'backgroundColor' => $colors['bleu'], // Couleur des points
                    'borderColor' => $colors['bleu'], // Couleur de la ligne
                    'data' => $data,
                    'fill' => 'false',
                    'tension' => 0.25,
                ],
            ],
        ]);
        $chart->setOptions([
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ]);

        return $this->render(view: 'admin/index.html.twig', parameters: [
            'totalUsers' => $totalUsers,
            'invitationsAccepted' => $invitationsAccepted,
            'invitationsNotAccepted' => $invitationsNotAccepted,
            'totalMessages' => $totalMessages,
            'totalLights' => $totalLights,
            'chart' => $chart,
        ]);
    }
}
