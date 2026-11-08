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
    private const array MONTHS_FR = [
        'Janvier',
        'Février',
        'Mars',
        'Avril',
        'Mai',
        'Juin',
        'Juillet',
        'Août',
        'Septembre',
        'Octobre',
        'Novembre',
        'Décembre',
    ];

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
        $totalUsers = $this->userRepository->countUsers();
        $invitationsAccepted = $this->invitationRepository->countInvitationsAccepted();
        $invitationsNotAccepted = $this->invitationRepository->countInvitationsNotAccepted();
        $totalMessages = $this->messageRepository->countMessages();
        $totalLights = $this->lightRepository->countLights();

        $messagesByMonth = $this->messageRepository->countMessagesByMonth();
        $usersByMonth = $this->userRepository->countUsersByMonth();
        $labels = $this->monthlyLabels(array_keys(array: $messagesByMonth));

        $colors = [
            'vert' => 'rgb(0, 210, 100)',
            'bleu' => 'rgb(54, 162, 235)',
            'orange' => 'rgb(255, 159, 64)',
        ];

        $messagesChart = $chartBuilder->createChart(type: Chart::TYPE_LINE);
        $messagesChart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Messages envoyés',
                    'backgroundColor' => $colors['bleu'],
                    'borderColor' => $colors['bleu'],
                    'data' => array_values(array: $messagesByMonth),
                    'tension' => 0.25,
                ],
            ],
        ]);
        $messagesChart->setOptions([
            'scales' => [
                'y' => ['beginAtZero' => true],
            ],
        ]);

        $usersChart = $chartBuilder->createChart(type: Chart::TYPE_LINE);
        $usersChart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Inscriptions',
                    'backgroundColor' => $colors['vert'],
                    'borderColor' => $colors['vert'],
                    'data' => array_values(array: $usersByMonth),
                    'tension' => 0.25,
                ],
            ],
        ]);
        $usersChart->setOptions([
            'scales' => [
                'y' => ['beginAtZero' => true],
            ],
        ]);

        $invitationsChart = $chartBuilder->createChart(type: Chart::TYPE_DOUGHNUT);
        $invitationsChart->setData([
            'labels' => ['Acceptées', 'En attente'],
            'datasets' => [
                [
                    'data' => [$invitationsAccepted, $invitationsNotAccepted],
                    'backgroundColor' => [$colors['vert'], $colors['orange']],
                    'borderColor' => [$colors['vert'], $colors['orange']],
                ],
            ],
        ]);

        return $this->render(view: 'admin/index.html.twig', parameters: [
            'totalUsers' => $totalUsers,
            'invitationsAccepted' => $invitationsAccepted,
            'invitationsNotAccepted' => $invitationsNotAccepted,
            'totalMessages' => $totalMessages,
            'totalLights' => $totalLights,
            'messagesChart' => $messagesChart,
            'usersChart' => $usersChart,
            'invitationsChart' => $invitationsChart,
        ]);
    }

    /**
     * Transforme des clés 'Y-m' en libellés 'Mois Année' en français.
     *
     * @param string[] $yearMonths
     *
     * @return string[]
     */
    private function monthlyLabels(array $yearMonths): array
    {
        return array_map(
            callback: static function (string $yearMonth): string {
                [$year, $month] = explode(separator: '-', string: $yearMonth);

                return self::MONTHS_FR[(int) $month - 1].' '.$year;
            },
            array: $yearMonths
        );
    }
}
