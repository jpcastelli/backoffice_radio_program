<?php

namespace Vorterix\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vorterix\BackendBundle\Entity\Top;
use Vorterix\BackendBundle\Entity\TopImage;

class TopController extends Controller
{
    protected $repository = "VorterixBackendBundle:Top";
   
    public function indexAction(){
        $tops = $this->getAll();
        return $this->render('VorterixBackendBundle:Top:index.html.twig', array('tops' => $tops));
    }
   
    public function newAction()
    {
        return $this->render('VorterixBackendBundle:Top:new.html.twig', array());
    }

    
    private function getTopOrder($a, $b){
        return $a < $b;
        
    }
    /**
     * @Route("/edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em             = $this->getDoctrine()->getManager();
        $top            = $em->getRepository($this->repository)->find($id);
        $topElements    = $top->getTopImages();
        $arrTopElements = array();
        $index          = 1;

        foreach($topElements as $element){
            $order = $element->getTopOrder();
            echo $order;
            $arrTopElements[($order == 0) ? $index : $order] = $element;
            $index++;
        }
        
        ksort($arrTopElements);
 
        return $this->render('VorterixBackendBundle:Top:edit.html.twig', array('top' => $top, 'topElements' => $arrTopElements));  
    }
            
    public function saveAction(Request $request){
        
        $title           = $request->request->get('top-name');
        $topTitles       = $request->request->get('top-titles');
        $topDescriptions = $request->request->get('top-descriptions');
        $topOrders       = $request->request->get('top-order');
        $topImages       = $request->request->get('top-images');
        $topImagesID     = $request->request->get('top-images-id');
 
        $em              = $this->getDoctrine()->getManager();
        
        if($request->request->get('top_id')){
            $id  = $request->request->get('top_id');
            $em  = $this->getDoctrine()->getManager();
            $top = $em->getRepository($this->repository)->find($id);
        }else{
            $top = new Top();
        }
        
        $top->setTitle($title);
        $arrTops = Array();
        $counter = 0;
        
        foreach($topTitles as $title){
            $arrTops[$counter]['id']          = (! empty($topImagesID[$counter])) ? $topImagesID[$counter] : '';
            $arrTops[$counter]['title']       = $title;
            $arrTops[$counter]['description'] = $topDescriptions[$counter];
            $arrTops[$counter]['order']       = $topOrders[$counter];
            $arrTops[$counter]['cover']       = $topImages[$counter];
            $counter++;
        }
        
        $this->saveTopImages($top, $arrTops);
        
        $em->persist($top);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add('success','Perfecto! Top ha sido guardada exitosamente.');
        return $this->redirect($this->generateUrl('VorterixBackendBundle_top', array()));
    }
    
    private function saveTopImages($top, $topElems){
        
        $em = $this->getDoctrine()->getManager();
        
        foreach($topElems as $topElem){
            $topImage  = (! empty($topElem['id'])) ? $em->getRepository('VorterixBackendBundle:TopImage')->find($topElem['id']) : new TopImage();
            $imageName = $topElem['cover'];
            
            if(file_exists(__DIR__.'/../../../../web/uploads/temp/'.$imageName)){
                $file = new \Symfony\Component\HttpFoundation\File\File(__DIR__.'/../../../../web/uploads/temp/'.$imageName);           
                $file->move($this->getPath('top'), $imageName);
            }
            
            $topImage->setTitle($topElem['title']);
            $topImage->setDescription($topElem['description']);
            $topImage->setTopOrder($topElem['order']);
            $topImage->setName($topElem['cover']);
            $topImage->setTop($top);
            $top->addTopImage($topImage);
        }
        
        $em->persist($topImage);
        return;
    }

    private function getAll(){
        $tops = $this->getDoctrine()
                     ->getRepository($this->repository)
                     ->findAll();
        
        return $tops;
    }
    
    public function deleteAction($id){
        
        $em        = $this->getDoctrine()->getManager();
        $top       = $em->getRepository($this->repository)->find($id);
        $topImages = $top->getTopImages();
        
        $path = $this->getPath('top');
        
        foreach($topImages as $image){
            try{
                $name = $image->getName();
                unlink($path.$name);
                }  catch (\Exception $e){
                }
        }
              
        $em->remove($top);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add('success','Perfecto! Top ha sido eliminada exitosamente.');
        return $this->redirect($this->generateUrl('VorterixBackendBundle_top', array()));
    }
    
    public function removeImageAction($id){
        $em        = $this->getDoctrine()->getManager();
        $top       = $em->getRepository("VorterixBackendBundle:TopImage")->find($id);
        $path      = $this->getPath('top');
        
        try{
            $name = $top->getName();
            $em->remove($top);
            $em->flush();
            unlink($path.$name);
 
            return new Response(Response::HTTP_OK);
            }  catch (\Exception $e){
                return new Response(Response::HTTP_NOT_FOUND);
            }
    }
    
    public function listAction($id){
        $tops = $this->getAll();
        return $this->render('VorterixBackendBundle:Top:list.html.twig', array('tops'=>$tops,'selected_id' => $id));
    }
    
    /**
     * Returns gallery files path.
     * @return string
     */
     public function getPath($type){
         
         switch($type){
            
            case 'top': 
                $path = $this->getUploadsDir().'top/';
                break;
        }
                
        return $path;
    }
    
     private function getUploadsDir(){
        return __DIR__.'/../../../../web/uploads/';
    }

}
