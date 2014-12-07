<?php

namespace Vorterix\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\Query\Expr\Join;
use DateTime;

class PostController extends Controller
{
    protected $maxPostsBlock = 7;
    protected $totalPosts    = 28;
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
        
        $em   = $this->getDoctrine()->getManager();
        $usr  = $this->get('security.context')->getToken()->getUser();
        $user = $em->getRepository('VorterixBackendBundle:User')->find($usr->getId());
        $id   = '';
        //if post id exists is an update
        if($request->request->get('post_id')){
            $id   = $request->request->get('post_id');
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
        $post->setUser($user);
        if(!$id){//if Edit mode will not replace date.
            $post->setCreateD(new \DateTime("now"));
        }
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

        
        $this->generateAction($category_id);
        $this->generateAction(0);
        
        $this->get('session')->getFlashBag()->add('success','Perfecto! El post ha sido guardado exitosamente');
        return $this->redirect($this->generateUrl('VorterixBackendBundle_post', array()));
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
        $category = $post->getCategory(); 
        $category_id = $category->getId();
        
        $em->remove($post);
        $em->flush();
        
        $this->generateAction($category_id);
        $this->generateAction(0);
        
        $this->get('session')->getFlashBag()->add('success','Perfecto! El post ha sido eliminado exitosamente');
        return $this->redirect($this->generateUrl('VorterixBackendBundle_post', array()));
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
    
    /* ********************JSON CONTROLLER CODE******************** */
    public function generateAction($category){
       
        $opinionID         = $this->getCategoryIDByName('Opinion');
        $carteleraID       = $this->getCategoryIDByName('Cartelera');
        $excludeCategories = array($opinionID, $carteleraID);
        
        
        if($category == '0'){//Category HOME
            $posts = $this->getAllPostsJson($excludeCategories, 1, $this->totalPosts);
        }
        else{
            $posts = $this->getPostsByCategory($category, 0, $this->totalPosts);  
        }
 
        $this->generateOpinionJson($opinionID);
        $this->generateCarteleraJson($carteleraID); 
        $this->generateNotasXPrograma(); 
        $notasxbloque   = $this->getNotasxBloque();
        $ultimaNota     = $this->getUltimaNota();

        $postsBlock = 1;
        $postCount = 1;
        $postxblock = array();
        
        foreach($posts as $post){
            if($post['id'] != $ultimaNota[0]['id']){
                if($postsBlock <= $this->maxPostsBlock){
                    if($ultimaNota[0]['id'])
                    if($postCount <= $notasxbloque[$postsBlock]){
                        if($post['id'] != $ultimaNota[0]['id']){
                            $postxblock["notasbloque$postsBlock"][] = $post;
                        }
                        $postCount++;
                    }else{
                        $postCount=1;
                        $postsBlock++;
                        if($post['id'] != $ultimaNota[0]['id']){
                            $postxblock["notasbloque$postsBlock"][] = $post;
                        }
                        $postCount++;
                    }
                }
            }
        }
 
        $json = json_encode(
                Array(
                    'settings'          => $this->getSettings(),
                    'micrositios'       => $this->getMicroSites(),
                    'ultimanota'        => $this->getUltimaNota(),
                    $postxblock,
                    'bannertop'         => $this->getBannerTop(),
                    'notasbloqueleidas'    => $this->getNotasBloqueLeidas(),
                    'videosmasvistos'      => $this->getVideosMasVistos(),
                    'numColumnasDestacado' => $this->getNumeroColumnasDestacado()
                )
            );
 
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $path = $this->getPath($category);
        $fs->dumpFile($path, $json);
        
        //return $this->render('VorterixBackendBundle:Json:index.html.twig', array('status'=>true, 'message' => "Json Creado exitosamente"));  
    }

    private function generateOpinionJson($opinionID){
        $opinionPosts = $this->getPostsByCategory($opinionID);
        $json = json_encode( array( 'opinion' => $opinionPosts ) );
        $fs   = new \Symfony\Component\Filesystem\Filesystem();
        $fs->dumpFile(__DIR__."/../../../../web/uploads/json/opinion.json", $json);
    }
    
    private function generateCarteleraJson($carteleraID){
        $carteleraPosts = $this->getPostsByCategory($carteleraID);
        $json = json_encode( array( 'cartelera' => $carteleraPosts ) );
        $fs   = new \Symfony\Component\Filesystem\Filesystem();
        $fs->dumpFile(__DIR__."/../../../../web/uploads/json/cartelera.json", $json);
    }

    private function getPath($category){ 
  
        $filename = $this->getJsonName($category);
        $path = __DIR__."/../../../../web/uploads/json/$filename";
        
        return $path;
    }
    
    private function getJsonName($category){

        $file = '';
        switch ($category){
            case 0: 
                  $file = 'home.json';
                  break;
            case 3: 
                $file = 'acido.json';
                break;
            case 6: 
                $file = 'delicias.json';
                break;
            case 13: 
                $file = 'grupo_muerte.json';
                break;
            case 19: 
                $file = 'newsterix.json';
            break;
            case 20: 
                $file = 'guetap.json';
                break;
            case 21: 
                $file = 'malditos_nerds.json';
                break;
            case 25: 
                $file = 'cartelera.json';
            break;
            case 26: 
                $file = 'opinion.json';
            break;
            case 27: 
                $file = 'general.json';
            break;
            case 28: 
                $file = 'tmn.json';
            break;
            case 29: 
                $file = 'bestias.json';
            break;
            case 30: 
                $file = 'cheque.json';
            break;
            case 31: 
                $file = 'cuchillo.json';
            break;
            case 32: 
                $file = 'dobleonada.json';
            break;
            case 41:
                $file = 'notasxprograma.json';
                break;
        }

        return $file;
    }
        
    private function getSettings(){
        $arrmsj = Array();
        $arrmsj[] = "test1";
        $arrmsj[] = "test2";
        $arrmsj[] = "test3";

        $settings = Array('player'=>"player","hora"=>"15","mensajes" => $arrmsj );
        
        return $settings;
    }
    
    private function getMicroSites(){
        
        $micro1 = Array('id'=>"1","nombre"=>"nombre micro 1","thumb" => "elthumb1.png" );
        $micro2 = Array('id'=>"2","nombre"=>"nombre micro 2","thumb" => "elthumb2.png" );
        $micro3 = Array('id'=>"3","nombre"=>"nombre micro 3","thumb" => "elthumb3.png" );
        $losmicro = Array();
        $losmicro[] = $micro1;
        $losmicro[] = $micro2;
        $losmicro[] = $micro3;
        
        return $losmicro;
    }

    
    private function generateNotasXPrograma(){
         
        $em         = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('VorterixBackendBundle:Category')->findAll();
        $index      = 0;
        $programas  = array();

        foreach($categories as $category){
            $name = $category->getName();
            if($name != 'Opinion' && $name != 'Cartelera'){
                $niceUrlLower = strtolower($category->getName());
                $niceUrl      = str_replace(" ", "-", $niceUrlLower);
                $programas[$index]['id']          = $category->getId();
                $programas[$index]['programa']    = $category->getName();
                $programas[$index]['jsname']      = $this->getJsonName($category->getId());
                $programas[$index]['nice-url']      = $niceUrl;
                $programas[$index]['cover']       = $category->getCover();
                $programas[$index]['description'] = $category->getDescription();
                $programas[$index]['notas']       = $this->getPostsByCategory($category->getId(), 0, 5);
                $index++;
            }
        }
        
        $json = json_encode( Array( 'notasxprograma' => $programas) );
 
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->dumpFile(__DIR__."/../../../../web/uploads/json/notasxprograma.json", $json);
        return;
    }
    
    private function getUltimaNota(){ 
        
         $em   = $this->getDoctrine()->getManager();
        $posts  = $em
                ->createQueryBuilder()
                ->select('c.name as programa, q.id,q.pretitle, q.title, q.shortDescription, q.description, q.cover, q.status, q.createD, q.comments')
                ->from('VorterixBackendBundle:Post', 'q')
                ->innerJoin('VorterixBackendBundle:Category', 'c', Join::WITH, 'q.category = c.id')
                ->orderBy('q.createD', 'DESC')
                ->where('q.status=true' )
                ->setFirstResult( 1 )
                ->setMaxResults( 1 )
                ->getQuery()
                ->getResult();
        
        return $posts;
    }
    
    private function getNumeroColumnasDestacado(){
        $em   = $this->getDoctrine()->getManager();
        $key  = $em->getRepository("VorterixBackendBundle:Settings")->findBy(array("keySetting" => "DESTACADO_COLUMNAS"));
 
        return $key[0]->getValueSetting();
    }
    
    private function getVideosMasVistos(){
        $mli1 = Array('id'=>"1","titulo" => "mario entrevisto a quino","thumb"=>"nota8.png","visto"=>"25.68k","fecha"=>"12/07/14" );
        $mli2 = Array('id'=>"2","titulo" => "mario entrevisto a quino","thumb"=>"nota8.png","visto"=>"25.68k","fecha"=>"12/07/14" );
        $mli3 = Array('id'=>"3","titulo" => "mario entrevisto a quino","thumb"=>"nota8.png","visto"=>"25.68k","fecha"=>"12/07/14" );
        $mli4 = Array('id'=>"4","titulo" => "mario entrevisto a quino","thumb"=>"nota8.png","visto"=>"25.68k","fecha"=>"12/07/14" );
        $mli5 = Array('id'=>"5","titulo" => "mario entrevisto a quino","thumb"=>"nota8.png","visto"=>"25.68k","fecha"=>"12/07/14" );
        $mli6 = Array('id'=>"6","titulo" => "mario entrevisto a quino","thumb"=>"nota8.png","visto"=>"25.68k","fecha"=>"12/07/14" );
        $mli7 = Array('id'=>"7","titulo" => "mario entrevisto a quino","thumb"=>"nota8.png","visto"=>"25.68k","fecha"=>"12/07/14" );
        $vidvistos = Array();
        $vidvistos[] = $mli1;
        $vidvistos[] = $mli2;
        $vidvistos[] = $mli3;
        $vidvistos[] = $mli4;
        $vidvistos[] = $mli5;
        return $vidvistos;
    }
    
    private function getBannerTop(){
        $bnt1 = Array('id'=>"1","seccion"=>"seccion","titulo" => "titulo" );
        $bnt2 = Array('id'=>"2","seccion"=>"seccion","titulo" => "titulo" );
        $bnt3 = Array('id'=>"3","seccion"=>"seccion","titulo" => "titulo" );
        $bnt4 = Array('id'=>"4","seccion"=>"seccion","titulo" => "titulo" );
        $bntop = Array();
        $bntop[] = $bnt1;
        $bntop[] = $bnt2;
        $bntop[] = $bnt3;
        $bntop[] = $bnt4;
        return $bntop;
    }
    
    private function getNotasBloqueLeidas(){
        $mli1 = Array('id'=>"1","titulo" => "mario entrevisto a quino","thumb"=>"nota8.png","visto"=>"25.68k","fecha"=>"12/07/14" );
        $mli2 = Array('id'=>"2","titulo" => "mario entrevisto a quino","thumb"=>"nota8.png","visto"=>"25.68k","fecha"=>"12/07/14" );
        $mli3 = Array('id'=>"3","titulo" => "mario entrevisto a quino","thumb"=>"nota8.png","visto"=>"25.68k","fecha"=>"12/07/14" );
        $mli4 = Array('id'=>"4","titulo" => "mario entrevisto a quino","thumb"=>"nota8.png","visto"=>"25.68k","fecha"=>"12/07/14" );
        $mli5 = Array('id'=>"5","titulo" => "mario entrevisto a quino","thumb"=>"nota8.png","visto"=>"25.68k","fecha"=>"12/07/14" );
        $mli6 = Array('id'=>"6","titulo" => "mario entrevisto a quino","thumb"=>"nota8.png","visto"=>"25.68k","fecha"=>"12/07/14" );
        $mli7 = Array('id'=>"7","titulo" => "mario entrevisto a quino","thumb"=>"nota8.png","visto"=>"25.68k","fecha"=>"12/07/14" );
        $nleidas = Array();
        $nleidas[] = $mli1;
        $nleidas[] = $mli2;
        $nleidas[] = $mli3;
        $nleidas[] = $mli4;
        $nleidas[] = $mli5;
        $nleidas[] = $mli6;
        $nleidas[] = $mli7;
        return $nleidas;
    }
    
    private function getNotasxBloque(){
        $notasxbloque = Array();
 
        $notasxbloque[1] = 3;
        $notasxbloque[2] = 3;
        $notasxbloque[3] = 4;
        $notasxbloque[4] = 7;
        $notasxbloque[5] = 4;
        $notasxbloque[6] = 7;
        
        return $notasxbloque;
    }
    
    private function getAllPostsJson($excludeCategories, $offset = 1, $limit = null){ 
        
        $excluded = implode(',', $excludeCategories);
 
        $em   = $this->getDoctrine()->getManager();
        $posts  = $em
                ->createQueryBuilder()
                ->select('q.id,q.pretitle, q.title, q.shortDescription, q.description, q.cover, q.status, q.createD, q.comments')
                ->from('VorterixBackendBundle:Post', 'q')
                ->orderBy('q.createD', 'DESC')
                ->where('q.status=true' )
                ->where("q.category not in ($excluded)")
                ->setFirstResult( $offset )
                ->setMaxResults( $limit )
                ->getQuery()
                ->getResult();
        return $posts;
    }
    
    private function getPostsByCategory($category, $offset = 0, $limit = null){
        
        $em   = $this->getDoctrine()->getManager();
        $posts  = $em
                ->createQueryBuilder()
                ->select('q.id,q.pretitle, q.title, q.shortDescription, q.description, q.cover, q.status, q.createD, q.comments')
                ->from('VorterixBackendBundle:Post', 'q')
                ->where('q.category = :id')
                ->setParameter('id', $category)
                 ->orderBy('q.createD', 'DESC')
                ->setFirstResult( $offset )
                ->setMaxResults( $limit )
                ->getQuery()
                ->getResult();
        
        return $posts;
    }
    
    
    
    private function getCategoryIDByName($categoryName){
        $em       = $this->getDoctrine()->getManager();
        $category = $em->getRepository('VorterixBackendBundle:Category')->findOneByName($categoryName);

        return $category->getId();
    }
}