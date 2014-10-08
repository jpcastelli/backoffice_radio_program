<?php

namespace Vorterix\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class JsonController extends Controller
{
   
    public function indexAction()
    {
        return $this->render('VorterixBackendBundle:Json:index.html.twig', array());
    }
    
    public function generateAction(Request $request){
        $category = $request->request->get('post_category');
        $em   = $this->getDoctrine()->getManager();
        $posts  = $em
                ->createQueryBuilder()
                ->select('q.id,q.pretitle, q.title, q.shortDescription, q.description, q.cover, q.status, q.createD, q.comments')
                ->from('VorterixBackendBundle:Post', 'q')
                ->where('q.category = :id')
                ->setParameter('id', $category)
                ->getQuery()
                ->getResult();
        
        $json = json_encode(Array('settings'=>$this->getSettings(),'micrositios'=>  $this->getMicroSites(),
            'notasxprog'=>$this->getNotasxProg(),
            'ultimanota' =>$this->getUltimaNota(),
            'notasdestacadas' => $this->getNotaDestacada(),
            'posts'=>$posts,
            'bannertop'=>$this->getBannerTop(),
            'notasbloqueleidas'=>  $this->getNotasBloqueLeidas(),
            'videosmasvistos'=>  $this->getVideosMasVistos()), JSON_UNESCAPED_UNICODE);
 
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        switch ($category){
            case 1: 
                $fs->dumpFile(__DIR__.'/../../../../web/uploads/json/tmn.json', $json);
            break;
            case 3: 
                $fs->dumpFile(__DIR__.'/../../../../web/uploads/json/acido.json',  $json);
            break;
            case 6: 
                $fs->dumpFile(__DIR__.'/../../../../web/uploads/json/delicias.json',  $json);
            break;
            case 13: 
                $fs->dumpFile(__DIR__.'/../../../../web/uploads/json/grupo_muerte.json', $json);
            break;
            case 19: 
                $fs->dumpFile(__DIR__.'/../../../../web/uploads/json/newsterix.json', $json);
            break;
            case 20: 
                $fs->dumpFile(__DIR__.'/../../../../web/uploads/json/guetap.json', $json);
            break;
            case 21: 
                $fs->dumpFile(__DIR__.'/../../../../web/uploads/json/malditos_nerds.json', $json);
            break;
        
        }
        
        return $this->render('VorterixBackendBundle:Json:index.html.twig', array('status'=>true, 'message' => "Json Creado exitosamente"));  
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
        
        $notas1 = Array('id'=>"1","titulo"=>"nombre nota 1","copete"=>"copete nota 1","thumb" => "thumbn1.png" );
        $notas2 = Array('id'=>"2","titulo"=>"nombre nota 2","copete"=>"copete nota 2","thumb" => "thumbn2.png" );
        $notas3 = Array('id'=>"3","titulo"=>"nombre nota 3","copete"=>"copete nota 3","thumb" => "thumbn3.png" );

        $notasp1[] = $notas1;
        $notasp1[] = $notas2;
        $notasp1[] = $notas3;

        $notasp2[] = $notas1;
        $notasp2[] = $notas2;
        $notasp2[] = $notas3;

        $notasp3[] = $notas1;
        $notasp3[] = $notas2;
        $notasp3[] = $notas3;
        $programa1 = Array('programa'=>"Guetap","jsname"=>"guetap","id" => "1","logo"=>"guetap.jpg","info"=>"lunes a viernes de 9 a 13js.<br />Mario Pergolini<br />marcelo gatman<br />vanina parejas.","notas"=>$notasp1 );
        $programa2 = Array('programa'=>"Acido","jsname"=>"acido","id" => "2","logo"=>"guetap.jpg","info"=>"lunes a viernes de 9 a 13js.<br />Mario Pergolini<br />marcelo gatman<br />vanina parejas.","notas"=>$notasp2 );
        $programa3 = Array('programa'=>"Newsterix","jsname"=>"newsterix","id" => "3","logo"=>"guetap.jpg","info"=>"lunes a viernes de 9 a 13js.<br />Mario Pergolini<br />marcelo gatman<br />vanina parejas.","notas"=>$notasp3 );

        $losprog = Array();
        $losprog[] = $programa1;
        $losprog[] = $programa2;
        $losprog[] = $programa3;
        
        return $losprog;
    }
    
    private function getUltimaNota(){
        return $ultimanota = Array('id'=>"39393","titulo"=>"titulo de la ultima nota","copete"=>"copete ultima nota","thumb" => "elthumbultima.png" );
    }
    
    private function getNotaDestacada(){
        $notas1 = Array('id'=>"1","titulo"=>"nombre nota 1","copete"=>"copete nota 1","thumb" => "thumbn1.png" );
        $notas2 = Array('id'=>"2","titulo"=>"nombre nota 2","copete"=>"copete nota 2","thumb" => "thumbn2.png" );
        $notas3 = Array('id'=>"3","titulo"=>"nombre nota 3","copete"=>"copete nota 3","thumb" => "thumbn3.png" );
        $ntsd = Array();
        $ntsd[] = $notas1;
        $ntsd[] = $notas2;
        $ntsd[] = $notas3;

        return $ntsd;
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
}
