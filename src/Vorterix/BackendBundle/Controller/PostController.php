<?php

namespace Vorterix\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

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
        $title         = $request->request->get('post_title');
        $pretitle      = $request->request->get('post_pretitle');
        $shortDescription = $request->request->get('post_short_description');
        $description   = $request->request->get('post_description');
        $post_type     = $request->request->get('post_type');
        $category_id   = ($post_type == "category") ? $request->request->get('post_category') : null;
        $section_id    = ($post_type == "section")  ? $request->request->get('post_section')  : null;
        $top_id        = $request->request->get('post_top');
        $tags          = $request->request->get('tags');
        $galleries     = $request->request->get('post_galleries');
        $cover         = $request->request->get('post_cover');
        $video         = $request->request->get('post_video');
        $publishTime   = $request->request->get('publish-time');
        $publishDate   = $request->request->get('publish-date');
        $progD         = $this->getFormatedDateTime($publishDate, $publishTime);
        $comments      = ($request->request->get('post_comments') == 'on') ? true : false;
        $post_status   = ($request->request->get('post_status') != 'draft') ? true : false;

        //Get Category entity object
        $category = ($category_id != null || $category_id != '') ? $em->getRepository('VorterixBackendBundle:Category')->find($category_id) : null;
        $section  = ($section_id != null  || $section_id  != '') ? $em->getRepository('VorterixBackendBundle:Section')->find($section_id)   : null;
        $top      = ($top_id  != '') ? $em->getRepository('VorterixBackendBundle:Top')->find($top_id) : null;
        
        //Save tags array
        $this->saveTags($tags);
        
        //Setting Values
        $post->setTitle($title);
        $post->setPretitle($pretitle);
        $post->setDescription($description);
        $post->setShortDescription($shortDescription);
        $post->setCategory($category);
        $post->setSection($section);
        $this->setPostGalleries($post, $galleries);
        $post->setStatus($post_status);
        $post->setTop($top);
        $post->setCover($cover);
        $post->setMainVideo($video);
        $post->setComments($comments);
        $post->setCreateD(new \DateTime("now"));
        $post->setPublishD($progD);
        
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
        $selected_top = $post->getTop();
        
        return $this->render('VorterixBackendBundle:Post:edit.html.twig', array('post' => $post, 'tags' => $tags,
                                                                                'galleries' => $galleries));   
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
                     ->findBy(array(), array( 'createD' => 'DESC' ));
        
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
    
    private function getAllTops(){
        $em = $this->getDoctrine()->getManager();
        $tops = $em->getRepository('VorterixBackendBundle:Top')->findAll();
        return $tops;
    }
    
    private function setPostGalleries($post, $galleries){
        
        $postGalleries = $post->getGalleries();
        if(count($postGalleries) > 0){
            foreach ($post->getGalleries() as $postGallery){
                $post->removeGallery($postGallery);
            }
        }
        
        if(count($galleries) > 0){
            foreach($galleries as $gallery){ 
                $em = $this->getDoctrine()->getManager();
                $gallery = $em->getRepository('VorterixBackendBundle:Gallery')->find($gallery);
                $post->addGallery($gallery);
            }
        }
    }
    
    /**
     * Since separate date and time returns formated datetimes
     * @param type $date
     * @param type $time
     * @return \DateTime
     */
    private function getFormatedDateTime($date, $time){
        //get separate date valies
        $date = explode('/', $date);
        $year = $date[2];
        $month = $date[1];
        $day = $date[0];
        
        //get separate time values
        $time = explode(':', $time);
        $hour = $time[0];
        $minuteExp = explode(' ', $time[1]);
        $minute = $minuteExp[0];
 
        //create datetime object
        $datetime = new DateTime();
        $datetime->setTime($hour, $minute, '00');
        $datetime->setDate($year, $month, $day);
        
        return $datetime;
    }
    
    
    public function removeCoverAction(Request $request){
        $postID   = $request->request->get('postID');
        $filename = $request->request->get('filename');
        
        $em   = $this->getDoctrine()->getEntityManager();
        $post = $em->getRepository($this->repository)->find($postID);
        $post->setCover(null);
        
        $em->flush();
        
        try{
        if(unlink($this->getUploadsDir().'posts/cover/'.$filename))
           return new Response(Response::HTTP_OK);
        else
            return new Response(Response::HTTP_NOT_FOUND);
        }  catch (\Exception $e){
            return new Response(Response::HTTP_NOT_FOUND);
        }
        
    }
    
    private function getUploadsDir(){
        return __DIR__.'/../../../../web/uploads/';
    }
}