<?php

namespace App\Controller;
use App\Entity\Evenement;


use App\Entity\Reservation;
use App\Form\AddReservationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\EvenementRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;




class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function index(): Response
    {
        return $this->render('Front/reservation/index.html.twig', [
            'controller_name' => 'ReservationController',
        ]);
    }



    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }


    #[Route('/reservation/new/{id}', name: 'app_reservation_new')]
    public function newReservation(Request $req, EntityManagerInterface $em, $id): Response
    {
        // Retrieve the Evenement entity using the provided id
        $evenement = $em->getRepository(Evenement::class)->find($id);
    
        if (!$evenement) {
            // Handle the case where Evenement entity is not found
            throw $this->createNotFoundException('Evenement not found');
        }
    
        $res = new Reservation();
        
        // Set nom_rese_event to NomEvent value if it's not null
        $nomReseEvent = $evenement->getNomEvent();
        if ($nomReseEvent !== null) {
            $res->setNomReseEvent($nomReseEvent);
        }
    
        // Set id_reser_event to the Evenement entity
        $res->setIdReserEvent($evenement);
    
        $form = $this->createForm(AddReservationType::class, $res);
        $form->handleRequest($req);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $capacite = $evenement->getCapacite();
        $nbrPlaceReserv = $res->getNbrPlaceReserv();
        if ($capacite !== null && $nbrPlaceReserv !== null) {
            $evenement->setCapacite($capacite - $nbrPlaceReserv);
        }
        $this->sendConfirmationEmail($res->getEmail());



            $em->persist($res);
            $em->flush();
            
            // Add a success flash message
            $this->addFlash('success', 'Ajout avec Succès');
            return $this->redirectToRoute("MesReservations");
        }
    
        return $this->render('Front/reservation/AddReservation.html.twig', [
            'FRes' => $form->createView()
        ]);
    }



    private function sendConfirmationEmail(string $recipientEmail)
    {
        $email = (new Email())
            ->from('hamadimelek0@gmail.com')
            ->to($recipientEmail)
            ->subject('Confirmation de réservation')
            
            ->html(
                $this->renderView(
                    'Front/reservation/email.html.twig'
                )
            );
    


        $this->mailer->send($email);
    }



    #[Route("/EvenementsClientCards", name:"EvenementsClientCards")] 
    public function AfficheCard(EvenementRepository $EveRep, Request $request, PaginatorInterface $paginator): Response
    {
        $eventsQuery = $EveRep->findAll();

        // Paginate the events with 3 events per page
        $pagination = $paginator->paginate(
            $eventsQuery, // Query
            $request->query->getInt('page', 1), // Page number
            3 // Limit per page
        );
    
        return $this->render('Front/reservation/ListeEvenementCards.html.twig', [
            'controller_name' => 'ReservationController',
            'pagination' => $pagination,
        ]);
    }


    #[Route("/MesReservations", name:"MesReservations")] 
    public function MesReservations(ReservationRepository $EveRep): Response
    {
        return $this->render('Front/reservation/ListReservation.html.twig', [
            'controller_name' => 'ReservationController',
            'list' => $EveRep->findAll(),
        ]);
    }
    



 
    #[Route("/deleteReservation", name:"deleteRes")]
public function DeleteReserva(Request $Request, EntityManagerInterface $Em): Response
{
    $id = $Request->get("id");
    $reservation = $Em->getRepository(Reservation::class)->find($id);
    
    if (!$reservation) {
        // Handle the case where the reservation is not found
        throw $this->createNotFoundException('Reservation not found');
    }
    
    $evenement = $reservation->getIdReserEvent();
    $nbrPlaceReserv = $reservation->getNbrPlaceReserv();
    
    // Increase the capacity of the event by the number of places reserved
    if ($evenement && $nbrPlaceReserv !== null) {
        $evenement->setCapacite($evenement->getCapacite() + $nbrPlaceReserv);
    }
    
    // Remove the reservation
    $Em->remove($reservation);
    $Em->flush();
    
    // Redirect to the list of reservations
    return $this->redirectToRoute("MesReservations");
}




}
