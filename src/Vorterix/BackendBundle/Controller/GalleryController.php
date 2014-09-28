<?php

namespace Vorterix\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use \Vorterix\BackendBundle\Entity\Gallery;
use Vorterix\BackendBundle\Entity\Image;

class GalleryController extends Controller
{
    
    protected $repository = "VorterixBackendBundle:Gallery"; 
    
    public function indexAction(){
        $galleries = $this->getAllGalleries();
        return $this->render('VorterixBackendBundle:Gallery:index.html.twig', array('galleries' => $galleries));
    }
    
    public function newAction(){
        return $this->render('VorterixBackendBundle:Gallery:new.html.twig', array());
    }
    
    public function saveAction(Request $request){
    
        $galleryName = $request->request->get('gallery-name');
        $uploadedImages = $request->request->get('images');
        
        if($request->request->get('gallery_id')){
            $id          = $request->request->get('gallery_id');
            $em          = $this->getDoctrine()->getManager();
            $gallery     = $em->getRepository($this->repository)->find($id);
            $galleryImgs = $gallery->getImages();
            
            //get images assoc with gallery for checking existence.
            $galleryImgsArr = array();
            foreach($galleryImgs as $img){
                 $galleryImgsArr[] = $img->getName();
            }
        }else{
            $gallery = new Gallery();
        }
        
        $em = $this->getDoctrine()->getManager();
        
        $gallery->setName($galleryName);
        if(count($uploadedImages) > 0){
            foreach($uploadedImages as $uploadImage){
                $image = new Image();
                $arrImagePath = explode('/', $uploadImage);
                $name = $arrImagePath[count($arrImagePath)-1];
                if(isset($galleryImgsArr) && count($galleryImgsArr) > 0){//edit mode check for image existence.
                    if(!in_array($name, $galleryImgsArr)){
                        $image->setName($name);
                        $image->setGallery($gallery);
                        $gallery->addImage($image);
                    }
                }else
                {
                    $image->setName($name);
                    $image->setGallery($gallery);
                    $gallery->addImage($image);
                }
            }
            $em->persist($image);
        }
        
        $em->persist($gallery);
        $em->flush();
        
        return $this->redirect($this->generateUrl('VorterixBackendBundle_gallery', array()));
    }
    
     private function getAllGalleries(){
        $galleries = $this->getDoctrine()
                     ->getRepository($this->repository)
                     ->findAll();
        
        return $galleries;
    }
    
    public function deleteAction($id){
    
        $em   = $this->getDoctrine()->getEntityManager();
        $gallery = $em->getRepository($this->repository)->find($id);
        $em->remove($gallery);
        $em->flush();
        
        $galleries = $this->getAllGalleries();

        return $this->redirect($this->generateUrl('VorterixBackendBundle_gallery', array('success_gallery' => 'true',
                                                                                         'galleries' => $galleries )));
    }
    
     public function editAction($id){
         
         $em   = $this->getDoctrine()->getEntityManager();
        $gallery = $em->getRepository($this->repository)->find($id);

        $images = $gallery->getImages();
        /*
        $images = $this->getDoctrine()
                ->getRepository("VorterixBackendBundle:Image")
                ->findBy(array('gallery_id' => $gallery->getId()));
                echo "<pre>"; print_r($images);exit;
                
                $images->getG
        */
        return $this->render('VorterixBackendBundle:Gallery:edit.html.twig', array('gallery' => $gallery, 'images' => $images));   
    }
}
