<?php

namespace App\Controller;

use App\Entity\EventUser;
use App\Entity\Reservation;
use App\Form\EventUserType;
use App\Repository\EventUserRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
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
    public function getAll (EventUserRepository $repo): Response{
        $list = $repo->findAll(); /*select * from author*/
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
    public function updateEvent(Response $req, ManagerRegistry $manager,$id,EventUserRepository $repo): Response{
        $em = $manager -> getManager();
        $event = $repo->find($id);
       //Appel formulaire
       $form=$this->createForm(EventUserType::class,$event);
       $form->handleRequest($req);

    if($form->isSubmitted()){    
        
        $em->persist($event);///bring req
        $em->flush();
        return $this->redirectToRoute('eventuser_getall') ;
    }
    return $this->renderForm('event_user/add.html.twig',['f'=>$form]);
    }

    #[Route('/deleteEvent/{id}',name: 'event_delete')]
    public function deleteEventForm ( ManagerRegistry $manager, $id, EventUserRepository $repo): Response {

       

        $em=$manager->getManager();
            //specifier l'attribut qu'on a doit supprimer (id)
            $event=$repo->find($id);

           
            $em->remove($event);
            $em->flush();


        return $this->redirectToRoute('eventuser_getall');
    }

 
    

    #[Route('/list_resv/{id}', name: 'resv_afficeh')]
    function findByRev(EventUserRepository $repo,$id, ManagerRegistry $manager, ReservationRepository $reservationRepository)
    {
        $em = $manager -> getManager();
    $reservation = new Reservation;
    $event = $repo->find($id);
    $reservation->setcin(12345678);
    $reservation->setNomU('aziz');
    $reservation->setPrenomU('benslimene');
    $reservation->setEvent($event);
    
    $em->persist($reservation);
    $em->flush();
  
        $list = $reservationRepository->findById($id) ;
        return $this->render('event_user/getresv.html.twig', [
            'events' => $list,
        ]);

    }

}
