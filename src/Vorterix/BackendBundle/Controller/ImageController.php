<?php

namespace Vorterix\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImageController extends Controller
{
    protected $repository = "VorterixBackendBundle:Image"; 

    public function removeAction(Request $request){
        $id        = $request->request->get('elementID');
        $filename  = $request->request->get('filename');
        $type      = $request->request->get('type');
        $path      = $this->getPath($type);
        $error     = false;
        if($id){
            $em    = $this->getDoctrine()->getEntityManager();
            $image = $em->getRepository($this->repository)->find($id);

            $em->remove($image);
            $em->flush();     
        }
        
        try{
           unlink($path.$filename);
        }  catch (\Exception $e){
            $error = true;
        }
        
        return ($error) ? new Response(Response::HTTP_OK) : new Response(Response::HTTP_NOT_FOUND);
    }
    
    public function getPath($type){
        
        switch($type){
            case 'post': 
                $path = $this->getUploadsDir().'posts/cover/';
                break;
            case 'gallery': 
                $path = $this->getUploadsDir().'galleries/';
                break;
            case 'category':
                $path = $this->getUploadsDir().'categories/cover/';
                break;
        }
        return $path;
    }
    
    private function getUploadsDir(){
        return __DIR__.'/../../../../web/uploads/';
    }
}
