<?php

namespace Vorterix\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Gaufrette\Adapter\Ftp as FtpAdapter;

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
        $tags = $this->getAllTagsAction();
        return $this->render('VorterixBackendBundle:Post:new.html.twig', array('tags' => $tags));
    }
    
    public function saveAction(Request $request){
        
        //if post id exists is an update
        if($request->request->get('post_id')){
            $id   = $request->request->get('post_id');
            $em   = $this->getDoctrine()->getManager();
            $post = $em->getRepository('VorterixBackendBundle:Post')->find($id);
            
            if (!$post) {
                throw $this->createNotFoundException(
                    'No product found for id '.$id
                );
            }
        }else{//New Element
            $em = $this->getDoctrine()->getEntityManager();
            $post = new \Vorterix\BackendBundle\Entity\Post();
        }
        
        //Form Values
        $title       = $request->request->get('post_title');
        $pretitle    = $request->request->get('post_pretitle');
        $description = $request->request->get('post_description');
        $category_id = $request->request->get('post_category');  
        $tags        = $request->request->get('tags');

        //Get Category entity object
        $category = $em->getRepository('VorterixBackendBundle:Category')->find($category_id);
        
        //Save tags array
        $this->saveTags($tags);
        
        //Setting Values
        $post->setTitle($title);
        $post->setPretitle($pretitle);
        $post->setDescription($description);
        $post->setShortDescription("casasdasd");
        $post->setCategory($category);
        $post->setStatus("false");
        $post->setCover("dasdas");
        $post->setMainVideo("dasda");
        /** @todo Save Tag after editing. */
        foreach($tags as $tagName){ 
            $tag = $em->getRepository('VorterixBackendBundle:Tag')->findBy(array('name' => $tagName));
            $post->addTag(current($tag));
        }
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
        
        $tags = $this->getAllTagsAction();

        return $this->render('VorterixBackendBundle:Post:edit.html.twig', array('post' => $post, 'tags' => $tags));   
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
    
    private function saveTags($tags){
        $em = $this->getDoctrine()->getManager();
        
        foreach($tags as $tagName){
            if(!$em->getRepository('VorterixBackendBundle:Tag')->findBy(array('name' => $tagName))){
                $tagsObj = new \Vorterix\BackendBundle\Entity\Tag();

                $tagsObj->setName($tagName);
                $em->persist($tagsObj);
                $em->flush();
            }
        }
    }
    
    public function getAllTagsAction(){
        $em = $this->getDoctrine()->getManager();
        $tags = $em->getRepository('VorterixBackendBundle:Tag')->findAll();
        return $tags;
    }
    
}