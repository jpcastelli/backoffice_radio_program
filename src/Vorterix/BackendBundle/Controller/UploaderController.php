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

    public function ajUploadAction(Request $request){
        $uploadedFile = $request->files->get('the_file');
        $type = $request->request->get('type');
 
        $path = $this->getUploadsDir();
        
        switch($type){
            case 'post': 
                $path = $this->getUploadsDir().'posts/cover/';
                break;
            case 'gallery': 
                $path = $this->getUploadsDir().'galleries/';
                break;
        }
        //$fileExtension = strtolower($uploadedFile->guessExtension());
        $filename = $uploadedFile->getClientOriginalName();
        $destinationFolder = $path;
        $uploadedFile->move($destinationFolder, $filename);
        return new Response($filename,200);
    }
    
    private function getUploadsDir(){
        return __DIR__.'/../../../../web/uploads/';
    }
}
