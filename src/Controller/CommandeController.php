<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/commande', name: 'admin.commande.')]
class CommandeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CommandeRepository $commande): Response
    {
        $commandes = $commande->findAll();
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }
    #[Route('/create', name: 'create', methods: ['POST', 'GET'])]
    public function create(Request $request, EntityManagerInterface $em)
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $medicaments = $commande->getMedicaments();
            $medicamentsInfo = [];
            $MontantTotal = 0;

            foreach ($medicaments as $med) {
                if ($med->getQuantity() < 1) {
                    $this->addFlash('danger', 'Le médicament ' . $med->getName() . ' n\'est pas disponible en quantité suffisante.');
                    return $this->redirectToRoute('admin.commande.create');
                }
            }
            foreach ($medicaments as $med) {
                $newquantity = $med->getQuantity() - 1;
                $med->setQuantity($newquantity);
                $medicamentsInfo[] = [
                    'id' => $med->getId(),
                    'name' => $med->getName(),
                    'price' => $med->getPrice(),
                ];
                $MontantTotal += $med->getPrice();
            }

            $commande->setMedicamentsInfo($medicamentsInfo);
            $commande->setMontantTotal($MontantTotal);
            $em->persist($commande); 
            $em->flush();
            $this->addFlash('success', 'La commande a bien été créée.');
            return $this->redirectToRoute('admin.commande.index');
        }

        return $this->render('commande/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'delete',methods:['DELETE'],requirements:['id'=>Requirement::DIGITS])] //respecter les verbes http par exemple si quelqu'un partage le lien sela ne supprimera pas une recette
    public function delete(Request $request ,Commande $commande,EntityManagerInterface $em){
        $em->remove($commande);
        $medicaments = $commande->getMedicaments();
        foreach ($medicaments as $med) {
            $newquantity = $med->getQuantity() + 1;
            $med->setQuantity($newquantity);
        }
        $em->flush();
        $this->addFlash('danger','la commande a bien ete supprimer');//(key,value)
        return $this->redirectToRoute('admin.commande.index');
    }
}
