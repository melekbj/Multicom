<?php

namespace App\Controller;

use Knp\Snappy\Pdf;
use App\Entity\Clients;
use App\Entity\Commande;
use App\Entity\Materiel;
use App\Form\ClientType;
use App\Form\CommandeType;
use App\Form\MaterielType;
use App\Repository\UserRepository;
use App\Repository\ClientsRepository;
use App\Repository\MaterielRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    
    #[Route('/liste_des_clients', name: 'app_liste_clients')]
    public function ListeClients(PersistenceManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $users = $em->getRepository(Clients::class)->findAll();

        $clients = new Clients();
        $form = $this->createForm(ClientType::class, $clients);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $doctrine->getManager();
            $entityManager->persist($clients);
            $entityManager->flush();
            $this->addFlash('success', 'Client ajouté avec succès');
            return $this->redirectToRoute('app_liste_clients');
        }


        return $this->render('admin/clients/ListeClients.html.twig', [
            'controller_name' => 'AdminController',
            'users' => $users,  
            'addClient' =>$form->createView(),
        ]);
    }

    #[Route('/update_client/{id}', name: 'app_edit_client')]
    public function updateClient($id, Request $request, ClientsRepository $rep, PersistenceManagerRegistry $doctrine): Response
    {
        // récupérer la classe à modifier
        $clients = $rep->find($id);
        // créer un formulaire
        $form = $this->createForm(ClientType::class, $clients);
        // récupérer les données saisies
        $form->handleRequest($request);
        // vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // récupérer les données saisies
            $clients = $form->getData();
            // persister les données
            $rep = $doctrine->getManager();
            $rep->persist($clients);
            $rep->flush();
            //flash message
            $this->addFlash('success', 'Le client a été mis à jour avec succès!');
            return $this->redirectToRoute('app_liste_clients');
        }
        return $this->render('admin/clients/updateClient.html.twig', [
            'editForm' => $form->createView(),
        ]);
    }

    #[Route('/delete_client/{id}', name: 'app_delete_client')]
    public function deleteClient($id, ClientsRepository $rep, PersistenceManagerRegistry $doctrine ): Response
    {

        //recuperer la classe a supprimer
        $clients = $rep->find($id);
        $rep=$doctrine->getManager();
        //supprimer la classe        
        $rep->remove($clients);
        $rep->flush();
        //flash message
        $this->addFlash('success', 'Client removed!');
        return $this->redirectToRoute('app_liste_clients'); 
        
    }

   

    #[Route('/deleteM/{id}', name: 'app_delete_materiel')]
    public function deleteM($id, MaterielRepository $rep, PersistenceManagerRegistry $doctrine ): Response
    {

        //recuperer la classe a supprimer
        $materiels = $rep->find($id);
        $rep=$doctrine->getManager();
        //supprimer la classe        
        $rep->remove($materiels);
        $rep->flush();
        //flash message
        $this->addFlash('success', 'Materiel supprimé!');
        return $this->redirectToRoute('app_stock'); 
        
    }

    

    #[Route('/liste_des_materiels', name: 'app_stock')]
    public function stock(PersistenceManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $materiels = $em->getRepository(Materiel::class)->findAll();

        $stock = new Materiel();
        $form = $this->createForm(MaterielType::class, $stock);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $doctrine->getManager();
            $entityManager->persist($stock);
            $entityManager->flush();
            $this->addFlash('success', 'Materiel ajouté avec succès');
            return $this->redirectToRoute('app_stock');
        }


        return $this->render('admin/materiels/ListeMateriels.html.twig', [
            'controller_name' => 'AdminController',
            'materiels' => $materiels,  
            'addMateriel' =>$form->createView(),
        ]);
    }

    #[Route('/update_materiel/{id}', name: 'app_update_materiel')]
    public function updateM($id, Request $request, MaterielRepository $rep, PersistenceManagerRegistry $doctrine): Response
    {
        // récupérer la classe à modifier
        $materiels = $rep->find($id);
        // créer un formulaire
        $form = $this->createForm(MaterielType::class, $materiels);
        // récupérer les données saisies
        $form->handleRequest($request);
        // vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // récupérer les données saisies
            $materiels = $form->getData();
            // persister les données
            $rep = $doctrine->getManager();
            $rep->persist($materiels);
            $rep->flush();
            //flash message
            $this->addFlash('success', 'Le matériel a été mis à jour avec succès!');
            return $this->redirectToRoute('app_stock');
        }
        return $this->render('admin/materiels/EditMateriel.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    

    
    
    #[Route('/detail_client/{id}', name: 'app_more')]
    public function show($id,PersistenceManagerRegistry $doctrine): Response
    {
        // Retrieve the client information from the database based on the provided id
        $clients = $doctrine->getRepository(Clients::class)->find($id);

        // Check if the client exists
        if (!$clients) {
            throw $this->createNotFoundException('Client not found');
        }

        // Render the client information in a template
        return $this->render('admin/detailClient.html.twig', [
            'client' => $clients,
        ]);
    }

    #[Route('/generatePdf/{id}', name: 'generate_pdf')]
    public function generatePdf(Pdf $snappy, int $id, PersistenceManagerRegistry $doctrine)
    {
        // Fetch the user details from the database using the ID
        $user = $this->getUser();
    
        $entityManager = $doctrine->getManager();
        $clientRepository = $entityManager->getRepository(Clients::class);
        $client = $clientRepository->find($id);
    
        if (!$client) {
            throw $this->createNotFoundException('User not found with ID: ' . $id);
        }
    
        // Fetch the commandes associated with the client
        $commandes = $client->getCommandes();
    
        // Render the Twig template and pass the user details and the base64-encoded image
        $html = $this->renderView('/resources/devis.html.twig', [
            'client' => $client,
            'commandes' => $commandes,
        ]);
    
        $pdfContent = $snappy->getOutputFromHtml($html);
    
        // You can return the PDF as a response
        return new Response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="devis.pdf"',
            ]
        );
    }
    
    //generate addCommande function
    #[Route('/liste_commande', name: 'app_liste_commandes')]
    public function addCommande(PersistenceManagerRegistry $doctrine, Request $request): Response
    {
        $em = $doctrine->getManager();
        $commandes = $em->getRepository(Commande::class)->findAll();

        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $doctrine->getManager();
            $entityManager->persist($commande);
            $entityManager->flush();
            $this->addFlash('success', 'Commande ajouté avec succès');
            return $this->redirectToRoute('app_liste_commandes');
        }
        return $this->render('admin/listeCommandes.html.twig', [
            'commandes' => $commandes,
            'addCommande' =>$form->createView(),
        ]);


    }


}
