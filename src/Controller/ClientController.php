<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;



#[Route('/admin/clients', name: 'admin.clients.')]
class ClientController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ClientRepository $clientRepository,CommandeRepository $commandeRepository,EntityManagerInterface $em): Response
    {
        $clients = $clientRepository->findAll();
        foreach ($clients as $client) {
            $montantTotal = 0;
            $commandes = $commandeRepository->findByClientId($client->getId());
            if(!$commandes){
                $client->setMontantTotal(0);
            }
            foreach($commandes as $com){
                $medicaments = $com->getMedicamentsInfo();
                if(empty($medicaments)){
                    $client->setMontantTotal(0);
                }
                else{
                    foreach ($medicaments as $med) {
                        $montantTotal += $med['price']; 
                    }
                        $client->setMontantTotal($montantTotal);
                }
            }
        $em->flush($client);
        }
        return $this->render('client/index.html.twig', [
            'clients' => $clients,
        ]);
    }
    #[Route('/show-{id}', name: 'show')]
    public function show(?int $id, ClientRepository $clientRepository,CommandeRepository $commandeRepository): Response
    {
        $client = $clientRepository->find($id);
        // $commandes = $client->getCommandes();
        $commandes = $commandeRepository->findByClientId($client->getId());
        if (!$commandes) {
            // throw $this->createNotFoundException('Client non trouvé');
            return $this->render('client/introuvable.html.twig');
        }
        // $medicaments = $client->getMedicaments();
        // $montant = 0;
        // foreach($medicaments as $med){
        //     $montant += $med->getprice();
        // }
        return $this->render('client/show.html.twig', [
            'client' => $client,
            'commandes' => $commandes,
            // 'montant'=>$montant,
        ]);
    }

    #[Route('/create',name:'create',methods:['post','get'])]
    public function create(EntityManagerInterface $em,Request $request){
        $client = new Client();
        $form = $this->createForm(ClientType::class,$client);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            // $medicaments = $client->getMedicaments();
            // foreach($medicaments as $med){
            //     $new_quantity = $med->getQuantity()-1;
            //     $med->setQuantity($new_quantity);
            //     $em->persist($med);
            // }

            // $montant = 0;
            // foreach($medicaments as $med){
            //     $montant += $med->getprice();
            // }
            // $client->setMontantTotal($montant);
            $em->persist($client);
            $em->flush();
            $this->addFlash('success','le client a ete bien ajouter');
            return $this->redirectToRoute('admin.clients.index');
        }
        return $this->render('client/create.html.twig',[
            'form'=>$form
        ]);
    
    }
    #[Route('/{id}',name:'edit',methods:['post','get'])]
    public function edit(Client $client,EntityManagerInterface $em,Request $request){
        $form = $this->createForm(ClientType::class,$client);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            // $medicaments = $client->getMedicaments();
            // foreach($medicaments as $med){
            //     $new_quantity = $med->getQuantity()-1;
            //     $med->setQuantity($new_quantity);
            //     $em->persist($med);
            // }
            $em->flush();
            $this->addFlash('success','le client a ete bien modifier');
            return $this->redirectToRoute('admin.clients.index');
        }
        return $this->render('client/edit.html.twig',[
            'form'=>$form,
            'client'=>$client
        ]);
    }
    #[Route('/{id}',name:'delete',methods:['DELETE'])]
    public function delete(Client $client,EntityManagerInterface $em){
        $em->remove($client);
        $em->flush();
        $this->addFlash('danger','le client a bien ete supprimer');
        return $this->redirectToRoute('admin.clients.index');
    }
}
