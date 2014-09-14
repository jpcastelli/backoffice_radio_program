<?php

namespace Vorterix\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    protected $repository = "VorterixBackendBundle:Category";
   
    public function indexAction(){
        $categories = $this->getAllCategories();
        return $this->render('VorterixBackendBundle:Category:index.html.twig', array('categories' => $categories));
    }
   
    public function newAction()
    {
        return $this->render('VorterixBackendBundle:Category:new.html.twig', array());
    }
    
    public function saveAction(Request $request){
        
        if($request->request->get('category_id')){
            $id = $request->request->get('category_id');
            $em = $this->getDoctrine()->getManager();
            $category = $em->getRepository('VorterixBackendBundle:Category')->find($id);
            
            if (!$category) {
                throw $this->createNotFoundException(
                    'No product found for id '.$id
                );
            }
        }else{
            $em = $this->getDoctrine()->getEntityManager();
            $category = new \Vorterix\BackendBundle\Entity\Category();
        }
            
        $name = $request->request->get('category_name');
    
        $category->setName($name);
        
        $em->persist($category);
        $em->flush();
        
        $categories = $this->getAllCategories();
        
        $content = $this->renderView(
                    'VorterixBackendBundle:Category:index.html.twig',
                    array('success_post' => 'true',
                        'categories' => $categories)
                    );

        return new Response($content);
    }
    
    public function editAction($id){
        $category = $this->getDoctrine()
                     ->getRepository($this->repository)
                     ->findOneBy(array('id' => $id));
        
        return $this->render('VorterixBackendBundle:Category:edit.html.twig', array('category' => $category));   
    }
    
    public function deleteAction($id){
    
        $em = $this->getDoctrine()->getEntityManager();
        $category = $em->getRepository($this->repository)->find($id);
        $em->remove($category);
        $em->flush();
        
        $categories = $this->getAllCategories();

        $content = $this->renderView(
                        'VorterixBackendBundle:Category:index.html.twig',
                        array('success_post' => 'true',
                              'categories'   => $categories )
                    );
               
        return new Response($content);
    }
    
    private function getAllCategories(){
        $categories = $this->getDoctrine()
                     ->getRepository($this->repository)
                     ->findAll();
        
        return $categories;
    }
    
    public function listAction($id){
        $categories = $this->getAllCategories();
        return $this->render('VorterixBackendBundle:Category:category_list.html.twig', array('categories'=>$categories,'selected_id' => $id));
    }

}
