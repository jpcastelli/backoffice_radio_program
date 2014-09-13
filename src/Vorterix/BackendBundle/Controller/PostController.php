<?php

namespace Vorterix\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    
    protected $repository = "VorterixBackendBundle:Post";
    
    public function indexAction()
    {
        $posts = $this->getAllPosts();
        return $this->render('VorterixBackendBundle:Post:index.html.twig', array('posts' => $posts));
    }
    
    public function newAction()
    {
        return $this->render('VorterixBackendBundle:Post:new.html.twig', array());
    }
    
    public function saveAction(Request $request){
        
        if($request->request->get('post_id')){
            $id = $request->request->get('post_id');
            $em = $this->getDoctrine()->getManager();
            $post = $em->getRepository('VorterixBackendBundle:Post')->find($id);
            
            if (!$post) {
                throw $this->createNotFoundException(
                    'No product found for id '.$id
                );
            }
        }else{
            $em = $this->getDoctrine()->getEntityManager();
            $post = new \Vorterix\BackendBundle\Entity\Post();
        }
            
        $title = $request->request->get('post_title');
        $pretitle = $request->request->get('post_pretitle');
        $description = $request->request->get('post_description');
        
        
        $post->setTitle($title);
        $post->setPretitle($pretitle);
        $post->setDescription($description);
        $post->setShortDescription("casasdasd");
        $post->setStatus("false");
        $post->setCover("dasdas");
        $post->setMainVideo("dasda");
        $post->setCreateD(new \DateTime("now"));
        $post->setPublishD(new \DateTime("now"));
        
        $em->persist($post);
        $em->flush();
        
        $posts = $this->getAllPosts();
        
        $content = $this->renderView(
                    'VorterixBackendBundle:Post:index.html.twig',
                    array('success_post' => 'true',
                        'posts' => $posts)
                    );

        return new Response($content);
    }

    public function editAction($id){
        $post = $this->getDoctrine()
                     ->getRepository($this->repository)
                     ->findOneBy(array('id' => $id));
        
        return $this->render('VorterixBackendBundle:Post:edit.html.twig', array('post' => $post));   
    }
    
    public function deleteAction($id){
    
        $em   = $this->getDoctrine()->getEntityManager();
        $post = $em->getRepository($this->repository)->find($id);
        $em->remove($post);
        $em->flush();
        
        $posts = $this->getAllPosts();

        $content = $this->renderView(
                        'VorterixBackendBundle:Post:index.html.twig',
                        array('success_post' => 'true',
                              'posts'        => $posts )
                    );
               
        return new Response($content);
    }
    
    private function getAllPosts(){
        $posts = $this->getDoctrine()
                     ->getRepository($this->repository)
                     ->findAll();
        
        return $posts;
    }

}