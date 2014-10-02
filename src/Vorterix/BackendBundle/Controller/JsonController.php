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
   
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        switch ($category){
            case 1: 
                $fs->dumpFile(__DIR__.'/../../../../web/uploads/json/tmn.json',  json_encode($posts));
            break;
            case 3: 
                $fs->dumpFile(__DIR__.'/../../../../web/uploads/json/acido.json',  json_encode($posts));
            break;
            case 6: 
                $fs->dumpFile(__DIR__.'/../../../../web/uploads/json/delicias.json',  json_encode($posts));
            break;
            case 13: 
                $fs->dumpFile(__DIR__.'/../../../../web/uploads/json/grupo_muerte.json',  json_encode($posts));
            break;
            case 19: 
                $fs->dumpFile(__DIR__.'/../../../../web/uploads/json/newsterix.json',  json_encode($posts));
            break;
            case 20: 
                $fs->dumpFile(__DIR__.'/../../../../web/uploads/json/guetap.json',  json_encode($posts));
            break;
            case 20: 
                $fs->dumpFile(__DIR__.'/../../../../web/uploads/json/malditos_nerds.json',  json_encode($posts));
            break;
        
        }
        
        return $this->render('VorterixBackendBundle:Json:index.html.twig', array('status'=>true, 'message' => "Json Creado exitosamente"));  
        }

}
