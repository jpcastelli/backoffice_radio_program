<?php
//TODO: set $uploadsFolder as a private variable and reuse it.
namespace Vorterix\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Gaufrette\Adapter\Ftp as FtpAdapter;

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
    
    private function getUploadsDir(){
        return __DIR__.'/../../../../web/uploads/';
    }
}
