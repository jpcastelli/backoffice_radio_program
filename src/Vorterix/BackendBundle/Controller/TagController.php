<?php

namespace Vorterix\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class TagController extends Controller
{
    
    public function getAllTagsAction(){
        $em = $this->getDoctrine()->getEntityManager();
        $tags = $em->getRepository('VorterixBackendBundle:Tag')->findAll();
        return $tags;
    }
}
