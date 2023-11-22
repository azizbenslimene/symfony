<?php

namespace App\Controller;

use App\Entity\EventUser;
use App\Entity\Reservation;
use App\Form\EventUserType;
use App\Repository\EventUserRepository;
use App\Repository\ReservationRepository;


use Endroid\QrCodeBundle\Response\QrCodeResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function getAll (Request $request,EventUserRepository $repo): Response{
        $searchNom = $request->query->get('search_nom');
    $searchLieu = $request->query->get('search_lieu');

    $list = $repo->findBySearchCriteria($searchNom, $searchLieu);

        return $this->render('event_user/getall.html.twig',['events' => $list]);

}

#[Route('/addEventForm', name: 'author_add')]
public function addEvent(Request $req, ManagerRegistry $manager): Response{
    $em = $manager -> getManager();
    $eventuser = new EventUser;
   //Appel formulaire
   $form=$this->createForm(EventUserType::class,$eventuser);
   $form->handleRequest($req);

   if ($form->isSubmitted() && $form->isValid()) {
    $uploadedFile =  $form->get('image')->getData();
    // $uploadedFile = $req->request->all(); // $form['image']->getData();
    // dd($uploadedFile);
    // dd($uploadedFile['event_user']['image']);

    if ($uploadedFile) {
        $imageDirectory = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);; // Your specified image directory
        $newFilename = uniqid().'.'.$uploadedFile->guessExtension();
        // dd($newFilename);
        try {
            $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
            $uploadedFile->move($destination, $newFilename);
        } catch (FileException $e) {
            // Handle the file upload exception
        }

        $eventuser->setImage('uploads/'.$newFilename);
    }

    $em->persist($eventuser);
    $em->flush();
    return $this->redirectToRoute('eventuser_getall') ;
}
return $this->renderForm('event_user/add.html.twig',['f'=>$form]);
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


/*
#[Route('/deleteExpiredEvents', name: 'delete_expired_events')]
public function deleteExpiredEvents(EventUserRepository $eventUserRepository): Response
{
    // Appeler la fonction de suppression des événements expirés
    $eventUserRepository->deleteExpiredEvents();

    // Vous pouvez rediriger vers une page appropriée ou afficher un message
    return $this->redirectToRoute('eventuser_getall');
}*/
} 
       

    