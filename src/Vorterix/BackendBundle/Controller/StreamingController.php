<?php

namespace Vorterix\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \Vorterix\BackendBundle\Entity\Streaming;

class StreamingController extends Controller
{
    private $repository = "VorterixBackendBundle:Streaming";
    
    public function indexAction()
    {
        $streamings = $this->getAllStreamings();
        return $this->render('VorterixBackendBundle:Streaming:index.html.twig', array('streamings' => $streamings));
    }

    public function newAction()
    {
        return $this->render('VorterixBackendBundle:Streaming:new.html.twig', array());
    }

    /**
     * @Route("/edit")
     * @Template()
     */
    public function editAction($id)
    {
       $streaming = $this->getDoctrine()
                     ->getRepository($this->repository)
                     ->findOneBy(array('id' => $id));
        
        return $this->render('VorterixBackendBundle:Streaming:edit.html.twig', array('streaming' => $streaming));   
    }
            
    private function getAllStreamings(){
        
        $streamings = $this->getDoctrine()
             ->getRepository($this->repository)
             ->findAll();
        
        return $streamings;
    }        
    
    public function saveAction(Request $request){
        
        $name          = $request->request->get('streaming_name');
        $mainStreaming = $request->request->get('main_streaming');
        $background    = $request->request->get('backgroundImage');
        $image2        = $request->request->get('image2');
        $hashtag       = $request->request->get('twitter_hashtag');
        $twFeed        = $request->request->get('twitter_feed');
        $streamCam1    = $request->request->get('cam1_url');
        $streamCam2    = $request->request->get('cam2_url');
        $streamCam3    = $request->request->get('cam3_url');
        $streamCam4    = $request->request->get('cam4_url');
        $id            = $request->request->get('streaming_id');
        $em            = $this->getDoctrine()->getManager();
        
        if($id != ''){
            
            $streaming = $em->getRepository($this->repository)->find($id);
        }else{
            $streaming = new Streaming();
        }
        
        $streaming->setMainStreaming($mainStreaming);
        $streaming->setName($name);
        $streaming->setImagen($image2);
        $streaming->setHashtag($hashtag);
        $streaming->setTwFeed($twFeed);
        $streaming->setStreamCam1($streamCam1);
        $streaming->setStreamCam2($streamCam2);
        $streaming->setStreamCam3($streamCam3);
        $streaming->setStreamCam4($streamCam4);
        
        if($background && $this->moveFiles($background))
            $streaming->setBackground($background);
        else
            $streaming->setBackground(null);
        
        if($image2 && $this->moveFiles($image2))
            $streaming->setImagen($image2);
        else
            $streaming->setImagen(null);
        
        $em->persist($streaming);
        $em->flush();
        
        $this->generateAction();
        
        $this->get('session')->getFlashBag()->add('success','Perfecto! La transmisión ha sido generada exitosamente');
        return $this->redirect($this->generateUrl('VorterixBackendBundle_streaming', array()));
    }
    
    /**
     * Move images from temp folder to streaming
     */
    private function moveFiles($filename){
        
        $tempPath   = $this->getPath('temp');
        $streamPath = $this->getPath('streaming');
        
        if(file_exists($tempPath.$filename)){  
            $file = new \Symfony\Component\HttpFoundation\File\File($tempPath.$filename);           
            if($file->move($streamPath, $filename))
               return true;
        }
        
        if(file_exists($streamPath.$filename)){
               return true;
        }
        
        return false;
    }
    
    public function deleteAction($id){
    
        $em   = $this->getDoctrine()->getEntityManager();
        $streaming = $em->getRepository($this->repository)->find($id);
        $em->remove($streaming);
        $em->flush();
     
        $this->generateAction();
        $this->get('session')->getFlashBag()->add('success','Perfecto! La transmisión ha sido eliminada exitosamente');
        return $this->redirect($this->generateUrl('VorterixBackendBundle_streaming', array()));
    }
    
    public function getPath($type){
         
         switch($type){
            case 'temp':
                $path = $this->getUploadsDir().'temp/';
                break;
            case 'streaming': 
                $path = $this->getUploadsDir().'streaming/';
                break;
        }
                
        return $path;
    }
    
    private function getUploadsDir(){
        return __DIR__.'/../../../../web/uploads/';
    }
    
    /* ********************JSON CONTROLLER CODE******************** */
    public function generateAction(){
 
        $json = json_encode(
                Array(
                    'streamings' => $this->getStreamings()
                )
            );
 
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->dumpFile(__DIR__."/../../../../web/uploads/json/transmisiones.json", $json);
        
        return true; 
    }
    
    private function getStreamings(){
        
        $em            = $this->getDoctrine()->getManager();
        $streamings    = $em->getRepository('VorterixBackendBundle:Streaming')->findAll();
        $arrStreamings = Array();
        $counter       = 0;

        foreach($streamings as $streaming){
            $arrStreamings[$counter]['id']            = $streaming->getId();
            $arrStreamings[$counter]['mainStreaming'] = $streaming->getMainStreaming();
            $arrStreamings[$counter]['name']          = $streaming->getName();
            $arrStreamings[$counter]['background']    = $streaming->getBackground();
            $arrStreamings[$counter]['image2']        = $streaming->getImagen();
            $arrStreamings[$counter]['hash']          = $streaming->getHashtag();
            $arrStreamings[$counter]['feed']          = $streaming->getTwFeed();
            $arrStreamings[$counter]['cam1Url']       = $streaming->getStreamCam1();
            $arrStreamings[$counter]['cam2Url']       = $streaming->getStreamCam2();
            $arrStreamings[$counter]['cam3Url']       = $streaming->getStreamCam3();
            $arrStreamings[$counter]['cam4Url']       = $streaming->getStreamCam4();
            $counter++;
        }
        
        return $arrStreamings;
    }

}
