<?php

namespace App\Controller;

use App\Entity\Evenement;
use Symfony\Component\HttpFoundation\Request;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;



class EvenementController extends AbstractController
{

    #[Route('/ListEve', name:'ListEvenement')]
    public function getEvenemen(EvenementRepository $ER): Response
    {
        return $this->render('Back/Evenement/index.html.twig', [
            'controller_name' => 'EvenementController',
            'liste' => $ER->findall()
        ]);
    }






    
    #[Route('/calendar/events', name: 'calendar_events')]
    public function calendarEvents(EvenementRepository $ER): Response
    {
        $events = $ER->findAll();
    
        // Convert events to array of arrays in FullCalendar format
        $formattedEvents = [];
        foreach ($events as $event) {
            $formattedEvents[] = [
                'title' => $event->getNomEvent(), // Assuming you have a getTitle method on your Evenement entity
                'start' => $event->getDate_deb()->format('Y-m-d'), // Adjust this based on your start date property
                'end' => $event->getDate_fin()->format('Y-m-d'), // Adjust this based on your end date property
            ];
        }
    
        // Convert the formatted events array to JSON
        $jsonData = json_encode($formattedEvents);
    
        // Create a new Response with the JSON data and appropriate headers
        $response = new Response($jsonData);
        $response->headers->set('Content-Type', 'application/json');
    
        return $response;
    }



    #[Route('/update-event-date', name: 'update_event_date', methods: ['POST'])]
    public function updateEventDate(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Retrieve data from the AJAX request
        $eventId = $request->request->get('eventId');
        $startDate = new \DateTime($request->request->get('startDate'));
        $endDate = new \DateTime($request->request->get('endDate'));
    
        // Retrieve the event entity from the database
        $event = $entityManager->getRepository(Evenement::class)->find($eventId);
    
        // Check if the event exists
        if (!$event) {
            return new JsonResponse(['error' => 'Event not found'], Response::HTTP_NOT_FOUND);
        }
    
        try {
            // Update the event start and end dates
            $event->setDate_deb($startDate);
            $event->setDate_fin($endDate);
    
            // Persist the changes to the database
            $entityManager->flush();
    
            // Return a success response
            return new JsonResponse(['message' => 'Event dates updated successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            // Log the exception for debugging
            // You can use Symfony's logger service for this purpose
    
            // Return an error response
            return new JsonResponse(['error' => 'Error updating event dates'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    








    #[Route('/evenement/new', name: 'app_evenement_new')]
    public function AddEven(Request $request, EntityManagerInterface $Em): Response
    {
        $Ev= new Evenement();
        $Form=$this->createForm(EvenementType::class,$Ev);
    
        $Form->handleRequest($request);
        if($Form->isSubmitted() && $Form->isValid() ){
            try {
                $file = $Ev->getImage();
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                
                // Move the uploaded file to the desired directory
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
                
                // Set the image file name in the entity
                $Ev->setImage($fileName);
                
                // Persist the entity
                $Em->persist($Ev);
                $Em->flush();
                
                // Add a success flash message
               // $this->addFlash('success', 'Ajout avec Succès');
                
                // Redirect to the list of events
                return $this->redirectToRoute("ListEvenement");
            } catch (FileException $e) {
                // Handle file upload exception
                $this->addFlash('error', 'Une erreur sest produite lors du chargement de limage.');
            } 
        }
        
        // Render the form
        return $this->render('Back/evenement/AddEvent.html.twig', [
            'FEV' => $Form->createView()
        ]);
    }

    #[Route('/evenement', name: 'app_evenement')]
    public function index(): Response
    {
        return $this->render('Back/evenement/index.html.twig', [
            'controller_name' => 'EvenementController',
        ]);
    }



    #[Route('/deleteEvenement', name:'deleteEve')]
    public function DeleteEvenement(Request $request, EntityManagerInterface $entityManager): Response
    {
        $id = $request->get("idEvent");
        $repository = $entityManager->getRepository(Evenement::class);
        $evenement = $repository->find($id);
    
        if (!$evenement) {
            throw $this->createNotFoundException('No event found for id ' . $id);
        }

        $entityManager->remove($evenement);
        $entityManager->flush();
    
        $this->addFlash(
            'info',
            'Suppression avec Succès'
        );
    
        return $this->redirectToRoute("ListEvenement");
    }



    #[Route('/edit/{idEvent}', name: 'EditEvent')]
    public function EditEvenement(Request $request, $idEvent, EntityManagerInterface $entityManager): Response
    {
        $Ev = $entityManager->getRepository(Evenement::class)->find($idEvent);
        $Form = $this->createForm(EvenementType::class, $Ev);
    
        $Form->handleRequest($request);
        if ($Form->isSubmitted() && $Form->isValid()) {
            $file = $Ev->getImage();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
            } catch (FileException $e) {
                // ...handle exception
            }
    
            $Ev->setImage($fileName);
    
            $this->addFlash('message', 'Evenement ajouté avec succès');
            $entityManager->flush();
    
            $this->addFlash(
                'info',
                'Modification avec succès'
            );
    
            return $this->redirectToRoute("ListEvenement");
        }
    
        return $this->render('Back/Evenement/EditEvenement.html.twig', [
            'EVE' => $Form->createView()
        ]);
    }



 
    #[Route("/Stat", name: "Stat")]
    public function Stat(EvenementRepository $evenementRepository): Response
    {
        $events = $evenementRepository->findAll();
        $eventNames = [];
        $eventCapacities = [];
        
        foreach ($events as $event) {
            $eventNames[] = $event->getNomEvent();
            $eventCapacities[] = $event->getCapacite();
        }
        
        return $this->render('Back/Evenement/StatEvenement.html.twig', [
            'EevNom' => json_encode($eventNames),
            'EevCap' => json_encode($eventCapacities)
        ]);
    }

    #[Route('/event/{id}/generate-pdf', name: 'generate_event_pdf')]
    public function generateEventPdf($id, EntityManagerInterface $entityManager, Dompdf $dompdf): Response
    {
        // Retrieve event data based on the provided ID
        $event = $entityManager->getRepository(Evenement::class)->find($id);
    
        // Load HTML content with image path
        $imagePath = realpath($this->getParameter('kernel.project_dir').'/public/img/BourLaFourm.png');
        $htmlContent = $this->renderView('Front/reservation/pdf_template.html.twig', [
            'event' => $event,
            'imagePath' => $imagePath,
        ]);
    
        // Load HTML content into Dompdf
        $dompdf->loadHtml($htmlContent);
    
        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
    
        // Render the PDF
        $dompdf->render();
    
        // Return the generated PDF as response
        return new Response($dompdf->output(), Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="event.pdf"',
        ]);
    }
    




    
}
