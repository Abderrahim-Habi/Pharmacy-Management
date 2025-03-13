<?php

namespace App\Controller;

use App\Entity\Medicament;
use App\Form\MedicamentType;
use App\Repository\CategoryRepository;
use App\Repository\MedicamentRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;


#[Route('/admin/medicaments', name: 'admin.medicaments.')]
class MedicamentController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request,MedicamentRepository $repository,CategoryRepository $category,EntityManagerInterface $em): Response
    {
        $page = $request->query->get('page', 1); // On récupère le numéro de page depuis l'URL, par défaut on prend la page 1.
        $limit = 4; // Nombre de produits par page. 5 par défaut.
        $categoryid = $request->query->get('category');
        $medicaments = $repository->paginateMedicament($page,$limit,$categoryid);
        $maxpages = ceil($medicaments->count()/$limit); // On récupère le numéro de page 
        $pages = range(1,$maxpages);
        $cat = $category->findAll();
        
        return $this->render('medicament/index.html.twig', [
            'medicaments' => $medicaments,
            'category'=>$cat,
            'pages'=>$pages,
            'currentPage'=>$page
        ]);
    }
    
    #[Route('/create', name: 'create',methods:['post','get'])]
    public function create(Request $request,EntityManagerInterface $em){
        $medicament = new Medicament();
        $form=$this->createForm(MedicamentType::class,$medicament);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $file = $form->get('image')->getData();
            if($file){
                $filename = $medicament->getName().'.'.$file->getClientOriginalExtension();
                $file->move($this->getParameter('kernel.project_dir') . '/public/medicament/image', $filename);
                $medicament->setImage($filename);
            }
            $em->persist($medicament);//pointer la nouvelle donnees
            $em->flush();
            $this->addFlash('success','le produit a bien ete creer'); /* Important pour renvoyer un message*/
            return $this->redirectToRoute('admin.medicaments.index');
        }
        return $this->render('medicament/create.html.twig',[
            'form'=>$form
        ]);

    }
    #[Route('/{id}', name: 'edit',methods:['POST','GET'],requirements:['id'=>Requirement::DIGITS])]
    public function edit(Medicament $medicament,Request $request,EntityManagerInterface $em){ 
        // $imagePath = $this->getParameter('kernel.project_dir') . '/public/medicament/image/' . $medicament->getImage();
        // $medicament->setImage($imagePath);
        $form = $this->createForm(MedicamentType::class,$medicament);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si un fichier d'image a été téléchargé
            $file = $form->get('image')->getData();
        
            if ($file) {
                // Si un fichier d'image est présent, on supprime l'image existante et on ajoute la nouvelle
                $imagePath = $this->getParameter('kernel.project_dir') . '/public/medicament/image/' . $medicament->getImage();
                if ($medicament->getImage() && file_exists($imagePath)) {
                    unlink($imagePath);
                }
        
                $filename = $medicament->getName() . '.' . $file->getClientOriginalExtension();
                $file->move($this->getParameter('kernel.project_dir') . '/public/medicament/image', $filename);
                $medicament->setImage($filename);
            }
            $em->flush();
            $this->addFlash('success', 'Le produit a bien été modifié');
            return $this->redirectToRoute('admin.medicaments.index');
        }
        return $this->render('medicament/edit.html.twig',[
            'Medicaments'=>$medicament,
            'form'=>$form,
        ]);
    }
    #[Route('/{id}', name: 'delete',methods:['DELETE'],requirements:['id'=>Requirement::DIGITS])] //respecter les verbes http par exemple si quelqu'un partage le lien sela ne supprimera pas une recette
    public function delete(Request $request ,Medicament $medicament,EntityManagerInterface $em){
        $imagePath = $this->getParameter('kernel.project_dir') . '/public/medicament/image/' . $medicament->getImage();
        if ($medicament->getImage() && file_exists($imagePath)) {
            unlink($imagePath);
        }
        $em->remove($medicament);
        $em->flush();
        $this->addFlash('danger','le produit a bien ete supprimer');//(key,value)
        return $this->redirectToRoute('admin.medicaments.index');
    }
}
