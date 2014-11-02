<?php

namespace Vorterix\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;


class VideoController extends Controller
{
    
    protected $repository = 'VorterixBackendBundle:Video';
    /**
     * @Route("/save")
     * @Template()
     */
    public function saveAction(Request $request)
    {
        $uploadedFile  = $request->files->get('cover_file');    
        $path          = $this->getPath();
        $fileExtension = strtolower($uploadedFile->guessExtension());
        $newFilename   = date('d-m-Y H.i.s').'.'.$fileExtension;
        $result        = false;
        
        //If upload file success, save filename to database
        if($uploadedFile->move($path, $newFilename)){
            $em = $this->getDoctrine()->getManager();
            $video = new \Vorterix\BackendBundle\Entity\Video();
            $video->setCover($newFilename);
            $em->persist($video);
            $em->flush();
            $id = $video->getId();
            $result = true;
        }
        
        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/json');
        if($result){
            $response->setData(array('response' => Response::HTTP_OK, 'video_id' => $id, 'cover_name' => $newFilename));
        }else{
            $response->setData(array('response' => Response::HTTP_NOT_FOUND));
        }
        
        return $response;
    }
    
    public function removeCoverAction(Request $request){
        $id       = $request->request->get('video_id');
        $filename = $request->request->get('filename');
        $path     = $this->getPath();
        $em       = $this->getDoctrine()->getManager();
        $video    = $em->getRepository($this->repository)->find($id);
        $em->remove($video);
        $em->flush();
        
        if(\file_exists($path.$filename))
            if(unlink($path.$filename))
                return new Response(Response::HTTP_OK);
            else
                return new Response(Response::HTTP_NOT_FOUND);
    }

            
    private function getPath(){
        return __DIR__.'/../../../../web/uploads/video/cover/';
    }
}
