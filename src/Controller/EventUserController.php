<?php

namespace App\Controller;

use App\Entity\EventUser;
use App\Entity\Reservation;
use App\Form\EventUserType;
use App\Repository\EventUserRepository;
use App\Repository\ReservationRepository;


use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCodeBundle\Response\QrCodeResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\QrCode;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;


class EventUserController extends AbstractController
{
    #[Route('/event/user', name: 'app_event_user')]
    public function index(): Response
    {
        return $this->render('event_user/index.html.twig', [
            'controller_name' => 'EventUserController',
        ]);
    }
    

    #[Route('/EventgetAll', name: 'eventuser_getall')]
    public function getAll(Request $request, EventUserRepository $repo, ManagerRegistry $manager): Response
    {
        $searchNom = $request->query->get('search_nom');
        $searchLieu = $request->query->get('search_lieu');

        // Call the method to search with the provided criteria
        $list = $repo->findBySearchCriteria($searchNom, $searchLieu);
        $this->supprimerEvenementsExpirees($repo, $manager);

        // Check if the request is an AJAX request
        if ($request->isXmlHttpRequest()) {
            // Return a JSON response with the search results
            $events = [];
            foreach ($list as $event) {
                $events[] = [
                    'nom' => $event->getNom(),
                    'date' => $event->getDate(),
                    'lieu' => $event->getLieu(),
                    'description' => $event->getDescription(),
                    'image' => $event->getImage(),
                    'prix' => $event->getPrix(),
                    'editLink' => $this->generateUrl('event_update', ['id' => $event->getId()]),
                    'deleteLink' => $this->generateUrl('event_delete', ['id' => $event->getId()]),
                    'reserveLink' => $this->generateUrl('resv_affiche', ['id' => $event->getId()]),
                    'qrLink' => $this->generateUrl('event_generate_qr', ['id' => $event->getId()]),
                ];
            }
    
            // Return a JSON response with the search results
            return new JsonResponse(['events' => $events]);
        }

        // Render the template for non-AJAX requests
        return $this->render('event_user/getall.html.twig', ['events' => $list]);
    }

#[Route('/addEventForm', name: 'author_add')]
public function addEvent(Request $req, ManagerRegistry $manager): Response
{
    $em = $manager->getManager();
    $eventuser = new EventUser;

    
    $form = $this->createForm(EventUserType::class, $eventuser);
    $form->handleRequest($req);

    if ($form->isSubmitted() && $form->isValid()) {
        // Vérification des mots interdits dans la description
        $forbiddenWords = ['israel', 'america', 'kill'];
        $description = strtolower($eventuser->getDescription());

        foreach ($forbiddenWords as $word) {
            if (strpos($description, $word) !== false) {
                $this->addFlash('error', 'Faite Attention,La description contient des mots interdits.');
                
                // Vous pouvez rediriger vers le formulaire ou une autre page en cas d'erreur.
                return $this->redirectToRoute('author_add');
            }
        }

        // Gestion du téléchargement de l'image
        $uploadedFile = $form->get('image')->getData();

        if ($uploadedFile) {
            // Logique de téléchargement de l'image ici
            $imageDirectory = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = uniqid().'.'.$uploadedFile->guessExtension();

            try {
                $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
                $uploadedFile->move($destination, $newFilename);
            } catch (FileException $e) {
                // Gérer l'exception en cas d'échec du téléchargement
            }

            $eventuser->setImage('uploads/'.$newFilename);
        }

        // Persistez l'entité dans la base de données
        $em->persist($eventuser);
        $em->flush();

        return $this->redirectToRoute('eventuser_getall');
    }

    return $this->renderForm('event_user/add.html.twig', ['f' => $form]);
}

#[Route('/updateEvent/{id}', name: 'event_update')]
public function updateEvent(Request $request, ManagerRegistry $manager, $id, EventUserRepository $repo): Response
{
    $entityManager = $manager->getManager();
    $event = $repo->find($id);

    if (!$event) {
        throw $this->createNotFoundException('Événement non trouvé avec l\'id ' . $id);
    }

    // Appel du formulaire en incluant seulement les champs que vous voulez mettre à jour
    $form = $this->createFormBuilder($event)
        ->add('nom')
        ->add('date')
        ->add('lieu')
        ->add('description')
        ->add('prix')
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Persistez les modifications dans la base de données
        $entityManager->persist($event);
        $entityManager->flush();

        return $this->redirectToRoute('eventuser_getall');
    }

    return $this->render('event_user/update.html.twig', [
        'f' => $form->createView(),
    ]);
}


    #[Route('/deleteEvent/{id}',name: 'event_delete')]
    public function deleteEventForm ( ManagerRegistry $manager, $id, EventUserRepository $repo): Response {

       

        $em=$manager->getManager();
            //specifier l'attribut qu'on a doit supprimer (id)
            $eventadmin=$repo->find($id);

           
            $em->remove($eventadmin);
            $em->flush();


        return $this->redirectToRoute('eventuser_getall');
    }

 
    



#[Route('/list_resv/{id}', name: 'resv_affiche')]
public function afficher_reserv($id, EventUserRepository $repo, ManagerRegistry $manager, ReservationRepository $reservationRepository): Response
{
    $entityManager = $manager->getManager();
    $event = $repo->find($id);

    // Vérifier si le nombre de réservations est inférieur au maximum
    $maxReservations = 3; // Nombre maximum de réservations par événement

    if (count($event->getReservations()) < $maxReservations) {
        // Créer une nouvelle réservation pour cet événement
        $reservation = new Reservation();
        $reservation->setCin(12345678);
        $reservation->setNomU('aziz');
        $reservation->setPrenomU('benslimene');
        $reservation->setEvent($event);

        $entityManager->persist($reservation);
        $entityManager->flush();

         // Récupérer la liste de toutes les réservations
        $list = $reservationRepository->findAll();

        // Ajouter un message de succès
       

        // Rediriger vers la liste des événements après la réservation réussie
        return $this->render('event_user/getresv.html.twig', [
            'events' => $list,
        ]);
    } else {
        // Ajouter un message d'erreur si le nombre de réservations est complet
        $this->addFlash('error', 'Le nombre de places est complet pour cet événement.');

        // Rediriger vers la liste des événements sans passer à l'interface getresv.html.twig
        return $this->redirectToRoute('eventuser_getall');
    }
}




#[Route('/generate-pdf/{id}', name: 'generate_pdf')]
public function generatePdf($id, ReservationRepository $reservationRepository)
{
    // Récupérer la réservation depuis la base de données
    $reservation = $reservationRepository->find($id);

    // Générer le contenu HTML du PDF (vous devrez créer un fichier twig pour cela)
    $html = $this->renderView('event_user/pdf.html.twig', [
        'reservation' => $reservation,
    ]);

    // Utiliser la bibliothèque Dompdf pour générer le PDF
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Créer une réponse avec le contenu PDF et le type de contenu approprié
    $response = new Response($dompdf->output());
    $response->headers->set('Content-Type', 'application/pdf');
    $response->headers->set('Content-Disposition', 'inline; filename="reservation.pdf"');

    return $response;
}

private function supprimerEvenementsExpirees(EventUserRepository $eventUserRepository, ManagerRegistry $manager): void
{
    // Récupérer les événements expirés
    $evenementsExpirees = $eventUserRepository->findEvenementsExpirees();

    // Supprimer les événements expirés
    $entityManager = $manager->getManager();
    foreach ($evenementsExpirees as $evenement) {
        $entityManager->remove($evenement);
    }
    $entityManager->flush();
}


#[Route('/event/generate-qr/{id}', name: 'event_generate_qr')]
public function generateQrCode($id, EventUserRepository $repo): Response
{
    $event = $repo->find($id);

    // Générer le contenu du QR Code (utilisez toutes les informations de l'événement)
    $qrContent = sprintf(
        "Nom de l'événement:   %s\nDate: %s\nLieu: %s\nDescription: %s\nPrix: %s",
        $event->getNom(),
        $event->getDate(),
        $event->getLieu(),
        $event->getDescription(),
        $event->getPrix()
    );

    // Créer une instance de QrCode
    $qrCode = new QrCode($qrContent);

    // Retourner une réponse avec l'image du QR Code
    // Créer une instance de PngWriter pour générer le résultat sous forme d'image PNG
    $writer = new PngWriter();
    $result = $writer->write($qrCode);

    // Créer une réponse avec le résultat du QR Code
    $response = new Response($result->getString(), Response::HTTP_OK, [
        'Content-Type' => $result->getMimeType(),
    ]);

    return $response;
}
}


       

    