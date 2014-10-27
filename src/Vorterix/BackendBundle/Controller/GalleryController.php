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
    
        $galleryName       = $request->request->get('gallery-name');
        $images            = $request->request->get('image-gallery');
        $imagesDescription = $request->request->get('description-gallery');
        
        if($request->request->get('gallery_id')){
            $id            = $request->request->get('gallery_id');
            $em            = $this->getDoctrine()->getManager();
            $gallery       = $em->getRepository($this->repository)->find($id);
            $galleryImages = $gallery->getImages();
    
            foreach($galleryImages as $image){
                 $em->remove($image);
                 $em->flush();
            }
        }else{
            $gallery = new Gallery();
        }
        
        $em = $this->getDoctrine()->getManager();
 
        $gallery->setName($galleryName);
        
        if(count($images) > 0){
            $counter = 0;
            foreach($images as $imageName){
                $image = new Image();          
                $image->setName($imageName);
                $image->setDescription($imagesDescription[$counter]);
                $image->setGallery($gallery);
                $gallery->addImage($image);
            
                $counter++;
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
        $images = $gallery->getImages();
        $path = $this->getPath();
        foreach($images as $image){
            try{
                $name = $image->getName();
                unlink($path.$name);
                }  catch (\Exception $e){
                }
        }
        
        $em->remove($gallery);
        $em->flush();
        
        $galleries = $this->getAllGalleries();

        return $this->redirect($this->generateUrl('VorterixBackendBundle_gallery', array('success_gallery' => 'true',
                                                                                         'galleries' => $galleries )));
    }
    
     public function editAction($id){
         $em     = $this->getDoctrine()->getEntityManager();
        $gallery = $em->getRepository($this->repository)->find($id);
        $images  = $gallery->getImages();
  
        return $this->render('VorterixBackendBundle:Gallery:edit.html.twig', array('gallery' => $gallery, 'images' => $images));   
    }
    
     public function getPath(){  
        $path = __DIR__.'/../../../../web/uploads/galleries/';
                
        return $path;
    }
   
}
