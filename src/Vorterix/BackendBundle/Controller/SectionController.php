<?php

namespace Vorterix\BackendBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SectionController extends Controller
{
    protected $repository   = "VorterixBackendBundle:Section";
    protected $browserTitle = "Section";
    protected $title        = "Secciones";


    public function indexAction(){
        $sections = $this->getAllSections();
        return $this->render('VorterixBackendBundle:Section:index.html.twig', array(
                                                                                    'sections'     => $sections, 
                                                                                    'browserTitle' => $this->browserTitle,
                                                                                    'title'        => $this->title
                                                                                    ));
    }
   
    public function newAction()
    {
        return $this->render('VorterixBackendBundle:Section:new.html.twig', array());
    }
    
    public function saveAction(Request $request){
        
        if($request->request->get('section_id')){
            $id = $request->request->get('section_id');
            $em = $this->getDoctrine()->getManager();
            $section = $em->getRepository('VorterixBackendBundle:Section')->find($id);
            
            if (!$section) {
                throw $this->createNotFoundException(
                    'No product found for id '.$id
                );
            }
        }else{
            $em = $this->getDoctrine()->getEntityManager();
            $section = new \Vorterix\BackendBundle\Entity\Section();
        }
 
        $name        = $request->request->get('name');
        $description = $request->request->get('description');
        $cover       = $request->request->get('section_cover');
    
        $section->setName($name);
        $section->setDescription($description);
        $section->setCover($cover);
        $em->persist($section);
        $em->flush();
        
        $sections = $this->getAllSections();
        $this->get('session')->getFlashBag()->add('success','Perfecto! La seccion ha sido agregada exitosamente.');
        return $this->redirect( $this->generateUrl('VorterixBackendBundle_section', array('sections' => $sections)));

    }
    
    public function editAction($id){
        $section = $this->getDoctrine()
                     ->getRepository($this->repository)
                     ->findOneBy(array('id' => $id));
        
        return $this->render('VorterixBackendBundle:Section:edit.html.twig', array('section' => $section));   
    }
    
    public function deleteAction($id){
    
        $em = $this->getDoctrine()->getEntityManager();
        $section  = $em->getRepository($this->repository)->find($id);
        $path     = $this->getPath();
        
        if($section->getCover() != ''){
            $image = $section->getCover();
            unlink($path.$image);
        }
        
        $sections = $this->getAllSections();
        $em->remove($section);
        $em->flush();
 
        $this->get('session')->getFlashBag()->add('success','Perfecto! La seccion ha sido eliminada exitosamente.');
        return $this->redirect( $this->generateUrl('VorterixBackendBundle_section', array('sections' => $sections)));
 
    }
    
    private function getAllSections(){
        $categories = $this->getDoctrine()
                     ->getRepository($this->repository)
                     ->findAll();
        
        return $categories;
    }
    
    public function listAction($id){
        $categories = $this->getAllSections();
        return $this->render('VorterixBackendBundle:Section:list.html.twig', array('categories'=>$categories,'selected_id' => $id));
    }

    public function removeImageAction(Request $request){
        $id       = $request->request->get('sectionID');
        $filename = $request->request->get('filename');
        $path     = $this->getPath();
        
        if($id){
            $em    = $this->getDoctrine()->getEntityManager();
            $image = $em->getRepository($this->repository)->find($id);
            $image->setCover("");
            $em->flush();
        }
        
        try{
        if(unlink($path.$filename))
           return new Response(Response::HTTP_OK);
        else
            return new Response(Response::HTTP_NOT_FOUND);
        }  catch (\Exception $e){
            return new Response(Response::HTTP_NOT_FOUND);
        }
    }
    
    public function saveImageAction(Request $request){
        
        $uploadedFile  = $request->files->get('the_file');
        $id            = $request->request->get('sectionID');
        $path          = $this->getPath();
        $fileExtension = strtolower($uploadedFile->guessExtension());
        $newFilename   = date('d-m-Y H.i.s').'.'.$fileExtension;
        
        if($id){
            $em    = $this->getDoctrine()->getEntityManager();
            $image = $em->getRepository($this->repository)->find($id);
            $image->setCover($newFilename);
            $em->flush();
        }
        
        if($uploadedFile->move($path, $newFilename))
            return new Response($newFilename);
        return new Response(null);
        
    }
    
    public function getPath(){  
        $path = __DIR__.'/../../../../web/uploads/section/cover/';
                
        return $path;
    }
}