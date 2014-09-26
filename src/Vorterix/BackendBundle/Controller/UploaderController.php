<?php
//TODO: set $uploadsFolder as a private variable and reuse it.
namespace Vorterix\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UploaderController extends Controller
{
    
    /*
     * This method upload a file coming from upload request
     */

    public function ajUploadAction(Request $request)
    { //echo $this->container->get('kernel')->getRootDir(); exit;
        //fix uploadify by using token auth
       
        
        //name based on uniqid if not provided as parameter
        $galleryName = $request->request->get('galleryName');
        $uploadedFile = $request->files->get('the_file');
        //$fileExtension = strtolower($uploadedFile->guessExtension());
        $filename = $uploadedFile->getClientOriginalName();
        $destinationFolder = $this->container->get('kernel')->getRootDir()."/../web/uploads";
        $uploadedFile->move($destinationFolder, $filename);
        return new Response($filename,200);
    }
    
}
