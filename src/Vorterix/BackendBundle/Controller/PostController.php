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
        $galleries = $this->getAllGalleries();
        return $this->render('VorterixBackendBundle:Post:new.html.twig', array('tags' => $tags, 'galleries' => $galleries));
    }
    
    public function saveAction(Request $request){
        
        //if post id exists is an update
        if($request->request->get('post_id')){
            $id   = $request->request->get('post_id');
            $em   = $this->getDoctrine()->getManager();
            $post = $em->getRepository('VorterixBackendBundle:Post')->find($id);
            $postTags = $post->getTags();
            
            if (!$post) {
                throw $this->createNotFoundException(
                    'No product found for id '.$id
                );
            }
        }else{//New Element
            $em = $this->getDoctrine()->getManager();
            $post = new \Vorterix\BackendBundle\Entity\Post();
        }
        
        //Form Values
        $title       = $request->request->get('post_title');
        $pretitle    = $request->request->get('post_pretitle');
        $description = $request->request->get('post_description');
        $category_id = $request->request->get('post_category');  
        $tags        = $request->request->get('tags');
        $galleries   = $request->request->get('post_galleries');
        $cover       = $request->request->get('post_cover');

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
        $this->setPostGalleries($post, $galleries);
        $post->setStatus("false");
        $post->setCover($cover);
        $post->setMainVideo("dasda");
        $post->setCreateD(new \DateTime("now"));
        $post->setPublishD(new \DateTime("now"));
        
        /** @todo Improve saving tags after editing. */
        if(isset($postTags) && !empty($postTags)){//PostTags will be set if in edit mode.
            $postTagsArray = array();
            foreach($postTags as $tag){
                 $postTagsArray[] = $tag->getName();
            }
        }
        
        if(!empty($tags)){
            foreach($tags as $tagName){ 
                    $tag = $em->getRepository('VorterixBackendBundle:Tag')->findBy(array('name' => $tagName));
                    if(isset($postTagsArray)){//edit mode.
                        if(!in_array($tagName, $postTagsArray)){
                            $post->addTag(current($tag));
                        }
                    }else{
                        $post->addTag(current($tag));
                    }
            }
        }
        
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
        $galleries = $this->getAllGalleries();
        
        return $this->render('VorterixBackendBundle:Post:edit.html.twig', array('post' => $post, 'tags' => $tags, 'galleries' => $galleries));   
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
        if(!empty($tags)){
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
    }
    
    public function getAllTagsAction(){
        $em = $this->getDoctrine()->getManager();
        $tags = $em->getRepository('VorterixBackendBundle:Tag')->findAll();
        return $tags;
    }
    
    private function getAllGalleries(){
        $em = $this->getDoctrine()->getManager();
        $galleries = $em->getRepository('VorterixBackendBundle:Gallery')->findAll();
        return $galleries;
    }
    
    private function setPostGalleries($post, $galleries){
        if(count($galleries) > 0){
            foreach($galleries as $gallery){
                $em = $this->getDoctrine()->getManager();
                $gallery = $em->getRepository('VorterixBackendBundle:Gallery')->find($gallery);
                $postGalleriesArr = array();
                $postGalleries = $post->getGalleries();
                if(count($postGalleries) > 0){
                    foreach ($post->getGalleries() as $postGallery){
                        $postGalleriesArr[] = $postGallery;
                    }

                    if(!in_array($gallery, $postGalleriesArr)){
                        $post->addGallery($gallery);
                    }
                }
            }
        }
    }
    
    /*
    public function filesAction(Request $request){
        $dir = $request->request->get('dir');

        $host ="us.upload.octoshape.com";
        $username = "sion-vorterix2015";
        $password = "ggAhGyJD";
        
        $ftp = new FtpAdapter($dir, $host, array("username" => $username, "password" => $password));
        $files = $ftp->listDirectory('/');

           
           
            $dir = $ftp->listDirectory('/'); 
            $tree = '<ul class="jqueryFileTree" style="display: none;">';
            foreach($dir['dirs'] as $directory){
                $isDir = $ftp->isDirectory($directory);
                if($isDir){
                    $tree .= '<li class="directory collapsed"><a href="#" rel="/'.$directory.'">'.$directory.'</a>';
                    $files = $ftp->listDirectory($directory);
                    $tree .= '<ul class="jqueryFileTree" style="display: none;">';
                    foreach($files['keys'] as $file){
                        $tree .= '<li class="file ext_txt"><a href="#" rel="ftp://us.upload.octoshape.com/'.$directory.'/'.$file.'">'.$file.'</a></li>';
                    }
                    $tree .= '</ul>';
                    $tree .= '</li>';
                    
                }
            }
            $tree .= '</ul>';
            

            return new Response( json_encode( array('result' => $tree)) );
        }*/
    
}