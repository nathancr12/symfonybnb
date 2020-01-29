<?php

namespace App\Controller;

use App\Service\StatsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard_index")
     */
    public function index(EntityManagerInterface $manager, StatsService $statsService)
    {
        $users = $statsService->getUsersCount();
        $ads = $statsService->getAdsCount();
        $bookings = $statsService->getBookingsCount();
        $comments = $statsService->getCommentsCount();

        $bestAds = $statsService->getAdsStats('DESC');

        $worstAds = $statsService->getAdsStats('ASC');

        /*'stats' => [
            'users' => $users,
            'ads' => $ads,
            'bookings' => $bookings,
            'comments' => $comments
        ]*/
        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => compact('users', 'ads', 'bookings',  'comments'),
            'bestAds' => $bestAds,
            'worstAds' => $worstAds
        ]);
    }
}
