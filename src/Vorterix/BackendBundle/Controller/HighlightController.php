<?php

namespace Vorterix\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Vorterix\BackendBundle\Entity\Highlight;
use Vorterix\BackendBundle\Entity\Settings;

class HighlightController extends Controller
{
    
    protected $repository = "VorterixBackendBundle:Highlight";
    /**
     * @Route("/new")
     * @Template()
     */
    public function newAction()
    {
        return array(
                // ...
            );    }

    /**
     * @Route("/edit")
     * @Template()
     */
    public function editAction($id)
    {
        $highlight = $this->getDoctrine()->getRepository($this->repository)->find($id);
        
        
        return $this->render('VorterixBackendBundle:Highlight:edit.html.twig', array('highlight' => $highlight));    
        
    }

    /**
     * @Route("/index")
     * @Template()
     */
    public function indexAction()
    {
        $highlights = $this->getAllHighlights();
        $em   = $this->getDoctrine()->getManager();
        $key  = $em->getRepository("VorterixBackendBundle:Settings")->findBy(array("keySetting" => "DESTACADO_COLUMNAS"));
        if(count($key) > 0)
            $key = $key[0];
        
        return $this->render('VorterixBackendBundle:Highlight:index.html.twig', array('highlights' => $highlights, 'settingColumn' => $key));
        
    }
            
    public function saveAction(Request $request){
        
        $title       = $request->request->get('title');
        $link        = $request->request->get('link');
        $littleImage = $request->request->get('little_image');
        $bigImage    = $request->request->get('big_image');
        $columns     = $request->request->get('columns');
        
         if($request->request->get('highlight_id')){
            $id   = $request->request->get('highlight_id');
            $em   = $this->getDoctrine()->getManager();
            $highlight = $em->getRepository('VorterixBackendBundle:Highlight')->find($id);
            
            if (!$highlight) {
                throw $this->createNotFoundException(
                    'No product found for id '.$id
                );
            }
        }else{//New Element
            $em = $this->getDoctrine()->getManager();
            $highlight = new Highlight();
        }
        
        $highlight->setTitle($title);
        $highlight->setLink($link);
        $highlight->setColumns($columns);
        $highlight->setLittleImage($littleImage);
        $highlight->setBigImage($bigImage);
        $this->moveTempImages( array($littleImage, $bigImage) );
        $em->persist($highlight);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add('success','Perfecto! El Destacado ha sido guardada exitosamente.');
        return $this->redirect($this->generateUrl('VorterixBackendBundle_highlight', array()));
    }
    
    public function removeFileAction($type, $filename, Request $request){
        
        if($type == 'exist'){
            $id         = $request->request->get('id');
            $imageSize  = $request->request->get('size');
            $em         = $this->getDoctrine()->getEntityManager();
            $highlight  = $em->getRepository($this->repository)->find($id);
            $path       = $this->getPath();
            
            if($imageSize == 'big'){
                $highlight->setBigImage(null);
            }
            
            if($imageSize == 'little'){
                $highlight->setLittleImage(null);
            }
            
            $em->flush();
        }else{
            $path = $this->getPath('temp');
        }
 
         try{
                if(unlink($path.$filename))
                        return new Response(Response::HTTP_OK);
                }  catch (\Exception $e){
                    return new Response(Response::HTTP_NOT_FOUND);
                }
    }
    
    public function deleteAction($id){
    
        $em        = $this->getDoctrine()->getEntityManager();
        $highlight = $em->getRepository($this->repository)->find($id);
        $path      = $this->getPath();
        
        try{
            if($highlight->getLittleImage() != null){
                $filename = $highlight->getLittleImage();
               unlink($path.$filename);
            }
            
            if($highlight->getBigImage() != null){
                $filename = $highlight->getLittleImage();
               unlink($path.$filename);
            }
        }  catch (\Exception $e){
            return new Response(Response::HTTP_NOT_FOUND);
        }
        
        
        $em->remove($highlight);
        $em->flush();
        
        $highlights = $this->getAllHighlights();

        $content = $this->renderView(
                        'VorterixBackendBundle:Highlight:index.html.twig',
                        array( 'highlights' => $highlights )
                    );
        
        $this->get('session')->getFlashBag()->add('success','Perfecto! El Destacado ha sido eliminado exitosamente.');
        return new Response($content);
    }
    
    private function moveTempImages($images){
        
        foreach($images as $image){
            if($image != ""){
                if(file_exists(__DIR__.'/../../../../web/uploads/temp/'.$image)){
                    $file = new \Symfony\Component\HttpFoundation\File\File(__DIR__.'/../../../../web/uploads/temp/'.$image);           
                    $file->move($this->getPath(), $image);
                }
            }
        }
        
        
        return;
    }
    
    private function getPath($type=null){
        
        
        return ($type == 'temp') ? __DIR__.'/../../../../web/uploads/temp/' : __DIR__.'/../../../../web/uploads/destacados/';
    }
    
    private function getAllHighlights(){
        return $this->getDoctrine()->getRepository($this->repository)->findAll();
    }
    
    public function columnChangeAction(Request $request){
        
        $columns = $request->request->get('columns');
        $id      = $request->request->get('id');
  
        if($id != ''){
            $em   = $this->getDoctrine()->getManager();
            $key  = $em->getRepository("VorterixBackendBundle:Settings")->find($id);
            $key->setValueSetting($columns);
        }else{
            $em   = $this->getDoctrine()->getManager();
            $key  = $em->getRepository("VorterixBackendBundle:Settings")->findBy(array("keySetting" => "DESTACADO_COLUMNAS"));
            
            if($key == null){
                $key = new Settings();
                $key->setKeySetting('DESTACADO_COLUMNAS');
                $key->setValueSetting($columns);
            }
        }
 
        $em->persist($key);
        $em->flush();
        
        return new Response($key->getId());
    }

}
