<?php
//TODO: set $uploadsFolder as a private variable and reuse it.
namespace Vorterix\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Gaufrette\Adapter\Ftp as FtpAdapter;
use \Imagick;

class UploaderController extends Controller
{
    
    /*
     * This method upload a file coming from upload request
     */

    public function ajUploadAction(Request $request){
        
        $uploadedFile  = $request->files->get('the_file');
        $type          = $request->request->get('type');     
        $path = $this->getPath($type);
   
        $fileExtension = strtolower($uploadedFile->guessExtension());
        $newFilename   = date('d-m-Y H.i.s').'.'.$fileExtension;
        $uploadedFile->move($path, $newFilename);

        return new Response($newFilename);
    }
    
    public function getPath($type){
        
        switch($type){
            case 'post': 
                $path = $this->getUploadsDir().'posts/cover/';
                break;
            case 'gallery': 
                $path = $this->getUploadsDir().'galleries/';
                break;
            case 'category':
                $path = $this->getUploadsDir().'categories/cover/';
                break;
            case 'section':
                $path = $this->getUploadsDir().'section/cover/';
                break;
        }
        return $path;
    }
    
    public function removeImageAction(Request $request){
        $filename = $request->request->get('filename');
        $type     = $request->request->get('type');
        $path     =  $this->getPath($type);
        
        try{
        if(unlink($path.$filename))
           return new Response(Response::HTTP_OK);
        else
            return new Response(Response::HTTP_NOT_FOUND);
        }  catch (\Exception $e){
            return new Response(Response::HTTP_NOT_FOUND);
        }
    }
    
    public function fileTreeAction(Request $request){
        $host ="us.upload.octoshape.com";
        $username = "sion-vorterix2015";
        $password = "ggAhGyJD";
        
        $dir = $request->request->get('dir');
        $ftp = new FtpAdapter($dir, $host, array("username" => $username, "password" => $password));
        $files = $ftp->listDirectory('/');
        
        $tree = "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
        // All dirs
        foreach( $files['dirs'] as $file ) {

                if($ftp->isDirectory($file) ) {
                        $tree .= "<li class=\"directory collapsed\"><a href=\"#\" rel=\"" .$dir. $file . "/\">" . htmlentities($file) . "</a></li>";
                }
        }
        // All files
        foreach( $files['keys'] as $file ) {
                //if( file_exists($root.$file) && $file != '.' && $file != '..' && !is_dir($root.$file) ) {
                        $ext = preg_replace('/^.*\./', '', $file);
                        $tree .= "<li class=\"file ext_$ext\"><a href=\"#\" rel=\"" .$dir. htmlentities($file) . "\">" . htmlentities($file) . "</a></li>";
                //}
        }
        $tree .= "</ul>";
        
        return new Response( $tree );
    }
    
    public function cropImageAction(Request $request){
        $filename    = $request->request->get('filename');
        $coverXpos   = $request->request->get('x-pos');
        $coverYpos   = $request->request->get('y-pos');
        $coverWidth  = $request->request->get('coverWidth');
        $coverHeight = $request->request->get('coverHeight');
        $coverFolder = $this->getPath('post');

        $imagickObj = new \Imagick($coverFolder.$filename);
        $imagickObj->cropImage($coverWidth, $coverHeight, $coverXpos, $coverYpos);
        //unlink($coverFolder.$filename);
        
        if($imagickObj->writeimage($coverFolder.$filename))
           return new Response( Response::HTTP_OK );
        else
            return new Response( Response::HTTP_NOT_FOUND );
        
    }
    
    private function getUploadsDir(){
        return __DIR__.'/../../../../web/uploads/';
    }
}
