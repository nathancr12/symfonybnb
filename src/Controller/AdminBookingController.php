<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Service\PaginationService;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings/{page<\d+>?1}", name="admin_bookings_index")
     */
    public function index($page, PaginationService $pagination)
    {
        $pagination->setEntityClass(Booking::class)
        ->setPage($page)
        ->setLimit(10)
        ;

        return $this->render('admin/booking/index.html.twig',[
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet d'éditer une réservation
     * @Route("/admin/bookings/{id}/edit", name="admin_bookings_edit")
     *
     * @param Booking $booking
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Booking $booking, Request $request, EntityManagerInterface $manager){
        $form = $this->createForm(AdminBookingType::class, $booking, [
            'validation_groups' => ['Default']
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $booking->setAmount(0); // 0 = empty -> pour activer la fonction PrePersit de l'entité Booking

            $manager->persist($booking); // pas obligatoire de persist quand on fait une update
            $manager->flush();

            $this->addFlash(
                'success',
                "La réservation n°<strong>{$booking->getId()}</strong> a bien été modifiée"
            );

        }


        return $this->render('admin/booking/edit.html.twig',[
            'booking' => $booking,
            'myForm' => $form->createView()
        ]);

    }

    /**
     * Permet de supprimer une réservation
     *
     * @Route("/admin/bookings/{id}/delete", name="admin_bookings_delete")
     * 
     * @param Booking $booking
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Booking $booking, EntityManagerInterface $manager){
        $manager->remove($booking);
        $manager->flush();

        $this->addFlash(
            'success',
            "La réservation a bien été supprimée"
        );

        return $this->redirectToRoute("admin_bookings_index");
    }
}
