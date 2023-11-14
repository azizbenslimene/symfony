<?php

namespace App\Controller;

use App\Entity\EventAdmin;
use App\Entity\EventUser;
use App\Entity\Reservation;
use App\Form\EventAdminType;
use App\Form\ReservationType;
use App\Repository\EventAdminRepository;
use App\Repository\EventUserRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventAdminController extends AbstractController
{
    #[Route('/event/admin', name: 'app_event_admin')]
    public function index(): Response
    {
        return $this->render('event_admin/index.html.twig', [
            'controller_name' => 'EventAdminController',
        ]);
    }

    #[Route('/EventAdmingetAll', name: 'eventadmin_getall')]
public function getAll(EventAdminRepository $eventAdminRepo, EventUserRepository $eventUserRepo): Response
{
    // Récupérer les événements ajoutés par l'admin
    $adminEvents = $eventAdminRepo->findAll();

    // Récupérer les événements ajoutés par l'utilisateur
    $userEvents = $eventUserRepo->findAll();

    // Fusionner les deux tableaux d'événements
    $allEvents = array_merge($adminEvents, $userEvents);

    return $this->render('event_admin/getall.html.twig', ['events' => $allEvents]);
}




#[Route('/addEventAdminForm', name: 'eventadmin_add_form')]
public function addEventform(Request$req, ManagerRegistry $manager): Response{
    $em = $manager -> getManager();
    $eventadmin = new EventAdmin;
   //Appel formulaire
   $form=$this->createForm(EventAdminType::class,$eventadmin);
   $form->handleRequest($req);

   if ($form->isSubmitted() && $form->isValid()) {
    $uploadedFile =  $form->get('image_a')->getData();
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

        $eventadmin->setImageA('uploads/'.$newFilename);
    }

    $em->persist($eventadmin);
    $em->flush();
    return $this->redirectToRoute('eventadmin_getall') ;
}
return $this->renderForm('event_admin/add.html.twig',['f'=>$form]);
}

#[Route('/updateEventAdmin/{id}', name: 'eventadmin_update')]
public function updateEvent(Request $request, ManagerRegistry $manager, $id, EventAdminRepository $repo): Response
{
    $entityManager = $manager->getManager();
    $event = $repo->find($id);

    if (!$event) {
        throw $this->createNotFoundException('Événement non trouvé avec l\'id ' . $id);
    }

    // Appel du formulaire en incluant seulement les champs que vous voulez mettre à jour
    $form = $this->createFormBuilder($event)
        ->add('nom_a')
        ->add('date_a')
        ->add('lieu_a')
        ->add('description_a')
        ->add('prix_a')
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Persistez les modifications dans la base de données
        $entityManager->persist($event);
        $entityManager->flush();

        return $this->redirectToRoute('eventadmin_getall');
    }

    return $this->render('event_admin/update.html.twig', [
        'f' => $form->createView(),
    ]);
}


#[Route('/deleteEventAdmin/{id}', name: 'eventadmin_delete')]
public function deleteEventForm(ManagerRegistry $manager, $id, EventAdminRepository $repo): Response
{
    $em = $manager->getManager();

    // Recherche de l'événement avec l'ID spécifié
    $event = $repo->find($id);

    // Vérification si l'événement existe
    if (!$event) {
        throw $this->createNotFoundException('Événement non trouvé avec l\'id ' . $id);
    }

    // Suppression de l'événement
    $em->remove($event);
    $em->flush();

    return $this->redirectToRoute('eventadmin_getall');
}


#[Route('/getrev', name: 'rev_getall')]
    public function getrev (ReservationRepository $repo): Response{
        $list = $repo->findAll(); /*select * from author*/
        return $this->render('event_admin/getrevA.html.twig',['events' => $list]);

}

#[Route('/deleterevAdmin/{id}', name: 'eventadmin_deleterev')]
public function deleterev(ManagerRegistry $manager, $id, ReservationRepository $repo): Response
{
    $em = $manager->getManager();

    // Recherche de l'événement avec l'ID spécifié
    $event = $repo->find($id);

    // Vérification si l'événement existe
    if (!$event) {
        throw $this->createNotFoundException('Événement non trouvé avec l\'id ' . $id);
    }

    // Suppression de l'événement
    $em->remove($event);
    $em->flush();

    return $this->redirectToRoute('rev_getall');
}

#[Route('/update_reservation/{id}', name: 'eventadmin_updaterev')]
public function updaterevA(Request $request, ManagerRegistry $manager, $id, ReservationRepository $repo): Response
{
    $entityManager = $manager->getManager();
    $reservation = $repo->find($id);

    if (!$reservation) {
        throw $this->createNotFoundException('Réservation non trouvée avec l\'id ' . $id);
    }

    // Création du formulaire pour la mise à jour
    $form = $this->createForm(ReservationType::class, $reservation);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Persistez les modifications dans la base de données
        $entityManager->persist($reservation);
        $entityManager->flush();

        // Redirigez l'utilisateur vers la liste des réservations après la mise à jour
        return $this->redirectToRoute('rev_getall');
    }

    // Affichage du formulaire de mise à jour
    return $this->render('event_admin/updaterev.html.twig', [
        'f' => $form->createView(),
    ]);
}



}
