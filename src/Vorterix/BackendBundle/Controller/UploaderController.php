<?php
//TODO: set $uploadsFolder as a private variable and reuse it.
namespace Vorterix\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpFoundation\JsonResponse;
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
        $path          = $this->getPath($type);
   
        $fileExtension = strtolower($uploadedFile->guessExtension());
        $t = microtime(true);
        $newFilename   = date('YmdHis'.$t).'.'.$fileExtension;
        $uploadedFile->move($path, $newFilename);

        return new Response($newFilename);
    }
    
    public function getPath($type){
 
        switch($type){
            case 'post': 
                $path = $this->getUploadsDir().'posts/cover/';
                break;
            case 'gallery': 
                $path = $this->getUploadsDir().'temp/';
                break;
            case 'category':
                $path = $this->getUploadsDir().'categories/cover/';
                break;
            case 'section':
                $path = $this->getUploadsDir().'section/cover/';
                break;
            case 'gallery_video_cover':
                $path = $this->getUploadsDir().'video/cover/';
                break;
            case 'temp':
                $path = $this->getUploadsDir().'temp/';
                break;
        }
        
        return $path;
    }
    
    public function removeImageAction(Request $request){
        $filename = $request->request->get('filename');
        $type     = $request->request->get('type');
        $path     = $this->getPath($type);

        try{
        if(unlink($path.$filename))
           return new Response(Response::HTTP_OK);
        else
            return new Response(Response::HTTP_NOT_FOUND);
        }  catch (\Exception $e){
            return new Response(Response::HTTP_NOT_FOUND);
        }
    }
    
    public function removeTempImageAction($filename){
 
        $path     = $this->getPath('temp');

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
        $ftp = new FtpAdapter($dir, $host, array("username" => $username, "password" => $password, "passive" => true));
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
        
        $uploadedFile  = $request->files->get('file');     
        $path          = $this->getPath('post');
   
        $fileExtension = strtolower($uploadedFile->guessExtension());
        $newFilename   = date('d-m-Y H.i.s').'.'.$fileExtension;
        $uploadedFile->move($path, $newFilename);
        $response = new Response($newFilename);
        
        return $response; 
    }
    
    private function getUploadsDir(){
        return __DIR__.'/../../../../web/uploads/';
    }
}
