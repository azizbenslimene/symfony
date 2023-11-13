<?php

namespace App\Controller;

use App\Entity\EventAdmin;
use App\Form\EventAdminType;
use App\Repository\EventAdminRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function getAll (EventAdminRepository $repo): Response{
        $list = $repo->findAll(); /*select * from author*/
        return $this->render('event_admin/getall.html.twig',['events' => $list]);

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


}
