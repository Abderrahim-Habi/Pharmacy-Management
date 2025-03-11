<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/category', name: 'admin.category.')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $category): Response
    {
        $categories = $category->findAll();
        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }
    #[Route('/create', name: 'create',methods:['post','get'])]
    public function create(Request $request,EntityManagerInterface $em){
        $category = new Category();
        $form=$this->createForm(CategoryType::class,$category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($category);//pointer la nouvelle donnees
            $em->flush();
            $this->addFlash('success','la category a bien ete Creer'); /* Important pour renvoyer un message*/
            return $this->redirectToRoute('admin.category.index');
        }
        return $this->render('category/create.html.twig',[
            'form'=>$form
        ]);

    }
    #[Route('/{id}', name: 'edit',methods:['POST','GET'],requirements:['id'=>Requirement::DIGITS])]
    public function edit(Category $category,Request $request,EntityManagerInterface $em){ 
        $form = $this->createForm(CategoryType::class,$category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success','la category a bien ete modifie'); /* Important pour renvoyer un message*/
            return $this->redirectToRoute('admin.category.index');
        } 
        return $this->render('category/edit.html.twig',[
            'category'=>$category,
            'form'=>$form,
        ]);
    }
    #[Route('/{id}', name: 'delete',methods:['DELETE'],requirements:['id'=>Requirement::DIGITS])] //respecter les verbes http par exemple si quelqu'un partage le lien sela ne supprimera pas une recette
    public function delete(Request $request,Category $category,EntityManagerInterface $em){
        $medicaments = $category->getMedicaments();
        if($medicaments->count()>0){
            $this->addFlash('danger','la category est associe a un ou plusieur medicaments');
            return $this->redirectToRoute('admin.category.index');
        }
        else{
            $em->remove($category);
            $em->flush();
            $this->addFlash('danger','la category a bien ete supprimer');//(key,value)
            return $this->redirectToRoute('admin.category.index');
        }
       
    }
}
