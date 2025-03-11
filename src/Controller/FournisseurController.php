<?php

namespace App\Controller;

use App\Entity\Fournisseur;
use App\Form\FournisseurType;
use App\Repository\FournisseurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/foursnisseur', name: 'admin.fournisseur.')]
class FournisseurController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(FournisseurRepository $four): Response
    {
        $fournisseur = $four->findAll();
        return $this->render('fournisseur/index.html.twig', [
            'fournisseur' => $fournisseur,
        ]);
    }
    #[Route('/create', name: 'create',methods:['post','get'])]
    public function create(EntityManagerInterface $em,Request $request): Response
    {
        $fournisseur = new Fournisseur();
        $form=$this->createForm(FournisseurType::class,$fournisseur);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($fournisseur);//pointer la nouvelle donnees
            $em->flush();
            $this->addFlash('success','le Fournisseur a bien ete creer'); /* Important pour renvoyer un message*/
            return $this->redirectToRoute('admin.fournisseur.index');
        }
        return $this->render('fournisseur/create.html.twig', [
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: 'edit',methods:['post','get'],requirements:['id'=>Requirement::DIGITS])]
    public function edit(Fournisseur $fournisseur,EntityManagerInterface $em,Request $request): Response
    {
        $form=$this->createForm(FournisseurType::class,$fournisseur);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($fournisseur);//pointer la nouvelle donnees
            $em->flush();
            $this->addFlash('success','le fournisseur a bien ete modifier'); /* Important pour renvoyer un message*/
            return $this->redirectToRoute('admin.fournisseur.index');
        }
        return $this->render('fournisseur/edit.html.twig', [
            'form' => $form,
            'fournisseur'=>$fournisseur,
        ]);
    }
    #[Route('/{id}', name: 'delete',methods:['DELETE'],requirements:['id'=>Requirement::DIGITS])]
    public function delete(Fournisseur $fournisseur,EntityManagerInterface $em){
        $em->remove($fournisseur);
        $em->flush();
        $this->addFlash('danger','le fournisseur a bien ete supprimer');
        return $this->redirectToRoute('admin.fournisseur.index');
    }
}
