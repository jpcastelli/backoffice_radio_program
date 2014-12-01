<?php

namespace Vorterix\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class JsonController extends Controller
{
    protected $maxPostsBlock = 7;
    protected $totalPosts    = 28;
   
    public function indexAction()
    {
        return $this->render('VorterixBackendBundle:Json:index.html.twig', array());
    }
    
    public function generateAction(Request $request){
        
        $category          = $request->request->get('post_category'); 
        $opinionID         = $this->getCategoryIDByName('Opinion');
        $carteleraID       = $this->getCategoryIDByName('Cartelera');
        $excludeCategories = array($opinionID, $carteleraID);
        
        
        if($category == '0'){//Category HOME
            $posts = $this->getAllPosts($excludeCategories, 1, $this->totalPosts);
        }
        else{
            $posts = $this->getPostsByCategory($category, 1, $this->totalPosts);  
        }
 
        $postsOpinion   = $this->getPostsByCategory($opinionID);
        $postsCartelera = $this->getPostsByCategory($carteleraID); 
        $notasxbloque   = $this->getNotasxBloque();

        $postsBlock = 1;
        $postCount = 1;
        $postxblock = array();
        
        foreach($posts as $post){

            if($postsBlock <= $this->maxPostsBlock){
                if($postCount <= $notasxbloque[$postsBlock]){
                    $postxblock["notasbloque$postsBlock"][] = $post;
                    $postCount++;
                }else{
                    
                    $postCount=1;
                    $postsBlock++;
                    $postxblock["notasbloque$postsBlock"][] = $post;
                    $postCount++;
                }
            }
        }
 
        $json = json_encode(
                Array(
                    'settings'          => $this->getSettings(),
                    'micrositios'       => $this->getMicroSites(),
                    'notasxprog'        => $this->getNotasxProg(),
                    'ultimanota'        => $this->getUltimaNota(),
                    'notasdestacadas'   => $this->getNotaDestacada(),
                    $postxblock,
                    'bannertop'         => $this->getBannerTop(),
                    'notasbloqueleidas' => $this->getNotasBloqueLeidas(),
                    'videosmasvistos'   => $this->getVideosMasVistos(),
                    'postsCartelera'    => $postsCartelera,
                    'postsOpinion'      => $postsOpinion,
                    'secciones'         => $this->getSections(),
                    'numColumnasDestacado' => $this->getNumeroColumnasDestacado()
                )
            );
 
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $path = $this->getPath($category);
        $fs->dumpFile($path, $json);
        
        return $this->render('VorterixBackendBundle:Json:index.html.twig', array('status'=>true, 'message' => "Json Creado exitosamente"));  
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

    
    private function getNotasxProg(){
         
        $em         = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('VorterixBackendBundle:Category')->findAll();
        $index      = 0;
        $programas  = array();

        foreach($categories as $category){
            $name = $category->getName();
            if($name != 'Opinion' && $name != 'Cartelera'){
                $programas[$index]['id']          = $category->getId();
                $programas[$index]['programa']    = $category->getName();
                $programas[$index]['jsname']      = $this->getJsonName($category->getId());
                $programas[$index]['cover']       = $category->getCover();
                $programas[$index]['description'] = $category->getDescription();
                $programas[$index]['notas']       = $this->getPostsByCategory($category->getId(), 1, 5);
                $index++;
            }
        }
        
        return $programas;
    }
    
    private function getUltimaNota(){ 
        
         $em   = $this->getDoctrine()->getManager();
        $posts  = $em
                ->createQueryBuilder()
                ->select('c.name as programa, q.id,q.pretitle, q.title, q.shortDescription, q.description, q.cover, q.status, q.createD, q.comments')
                ->from('VorterixBackendBundle:Post', 'q')
                ->innerJoin('VorterixBackendBundle:Category', 'c')
                ->orderBy('q.publishD', 'DESC')
                ->where('q.status=true' )
                ->setFirstResult( 1 )
                ->setMaxResults( 1 )
                ->getQuery()
                ->getResult();
        
        return $posts;
    }
    
    private function getNotaDestacada(){
        
        $em            = $this->getDoctrine()->getManager();
        $highlights    = $em->getRepository('VorterixBackendBundle:Highlight')->findAll();
        $arrHighlights = Array();
        $counter       = 0;
        
        foreach($highlights as $highlight){
            $arrHighlights[$counter]['id']            = $highlight->getId();
            $arrHighlights[$counter]['titulo']        = $highlight->getTitle();
            $arrHighlights[$counter]['link']          = $highlight->getLink();
            $arrHighlights[$counter]['columnas']      = $highlight->getColumns();
            $arrHighlights[$counter]['imagen_chica']  = $highlight->getLittleImage();
            $arrHighlights[$counter]['imagen_grande'] = $highlight->getBigImage();
     
            $counter++;
        }
        return $arrHighlights;
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
    
    private function getAllPosts($excludeCategories, $offset = 1, $limit = null){ 
        
        $excluded = implode(',', $excludeCategories);
 
        $em   = $this->getDoctrine()->getManager();
        $posts  = $em
                ->createQueryBuilder()
                ->select('q.id,q.pretitle, q.title, q.shortDescription, q.description, q.cover, q.status, q.createD, q.comments')
                ->from('VorterixBackendBundle:Post', 'q')
                ->orderBy('q.publishD', 'DESC')
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
                 ->orderBy('q.publishD', 'DESC')
                ->setFirstResult( $offset )
                ->setMaxResults( $limit )
                ->getQuery()
                ->getResult();
        
        return $posts;
    }
    
    private function getSections(){
        
        $em       = $this->getDoctrine()->getManager();
        $sections = $em->getRepository('VorterixBackendBundle:Section')->findAll();
        $arrSections = Array();
        $counter = 0;
        foreach($sections as $section){
            $arrSections[$counter]['id']          = $section->getId();
            $arrSections[$counter]['name']        = $section->getName();
            $arrSections[$counter]['description'] = $section->getDescription();
            $arrSections[$counter]['cover']       = $section->getCover();
            
            $counter++;
        }
        return $arrSections;
    }
    
    private function getCategoryIDByName($categoryName){
        $em       = $this->getDoctrine()->getManager();
        $category = $em->getRepository('VorterixBackendBundle:Category')->findOneByName($categoryName);

        return $category->getId();
    }
    
    
}
