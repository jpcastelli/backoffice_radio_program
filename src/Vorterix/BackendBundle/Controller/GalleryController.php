<?php

namespace Vorterix\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use \Vorterix\BackendBundle\Entity\Gallery;
use Vorterix\BackendBundle\Entity\Image;
use Vorterix\BackendBundle\Entity\Video;

class GalleryController extends Controller
{
    
    protected $repository = "VorterixBackendBundle:Gallery"; 
    
    public function indexAction(){
        $galleries = $this->getAllGalleries();
        return $this->render('VorterixBackendBundle:Gallery:index.html.twig', array('galleries' => $galleries));
    }
    
    public function newAction(){
        return $this->render('VorterixBackendBundle:Gallery:new.html.twig', array());
    }
    
    /**
     * Saves full gallery including videos and images. New and edit mode.
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function saveAction(Request $request){
        //Gallery images requests.
        $galleryName       = $request->request->get('gallery-name'); 
        $audio             = ($request->request->get('audio-gallery') == 'on') ? 1 : 0;
        $images            = $request->request->get('image-gallery');
        $imagesDescription = $request->request->get('description-gallery');
        $imagesID          = $request->request->get('image-gallery-id');

        //Gallery videos requests.
        $videosCover       = $request->request->get('cover-video-gallery');
        $videosDescription = $request->request->get('description-video-gallery');
        $videosName        = $request->request->get('name-video-gallery');
        $videosID          = $request->request->get('video-gallery-id');
        $arrVideos         = Array();
        $index             = 0;
 
        foreach($videosName as $name){
            $arrVideos[$index]['id']          = (! empty($videosID[$index])) ? $videosID[$index] : '';
            $arrVideos[$index]['cover']       = (! empty($videosCover[$index])) ? $videosCover[$index] : null;
            $arrVideos[$index]['description'] = $videosDescription[$index];
            $arrVideos[$index]['name']        = $name;
            $index++;
        }
        
        if($request->request->get('gallery_id')){
            $id            = $request->request->get('gallery_id');
            $em            = $this->getDoctrine()->getManager();
            $gallery       = $em->getRepository($this->repository)->find($id);
        }else{
            $gallery = new Gallery();
        }
        
        $em = $this->getDoctrine()->getManager();
 
        $gallery->setName($galleryName);
        $gallery->setAudio($audio);
        $this->saveVideosGallery($gallery, $arrVideos, $audio);
        $this->saveImagesGallery($gallery, $images, $imagesDescription, $imagesID);
        
        $em->persist($gallery);
        $em->flush();
        
        $this->get('session')->getFlashBag()->add('success','Perfecto! La galeria ha sido guardada exitosamente.');
        return $this->redirect($this->generateUrl('VorterixBackendBundle_gallery', array()));
    }
    
    /**
     * Add videos to the current gallery.
     * @param type $gallery
     * @param type $videosCover
     * @param type $videosDescription
     * @param type $videosName
     */
     private function saveVideosGallery($gallery, $arrMedia, $isAudio){
 
        $em = $this->getDoctrine()->getManager();
        foreach($arrMedia as $media){
            $mediaObj = (! empty($media['id'])) ? $em->getRepository('VorterixBackendBundle:Video')->find($media['id']) : new Video();

            $mediaObj->setCover(($isAudio) ? null : $media['cover']);
            $mediaObj->setDescription($media['description']);
            $mediaObj->setName($media['name']);
            $mediaObj->setGallery($gallery);
            $gallery->addVideo($mediaObj);
        }
        $em->persist($mediaObj);
        return;
    }
    
    /**
     * Add images to the current gallery.
     * @param type $gallery
     * @param type $images
     * @param type $imagesDescription
     */
    private function saveImagesGallery($gallery, $images, $imagesDescription, $imagesID){
        
        $em = $this->getDoctrine()->getManager();
        if(count($images) > 0 && count($images) > count($imagesID)){
            $counter = 0;
            foreach($images as $imageName){
                if(!count($imagesID) || !array_key_exists($counter, $imagesID)){
                    if(file_exists(__DIR__.'/../../../../web/uploads/temp/'.$imageName)){
                        $file = new \Symfony\Component\HttpFoundation\File\File(__DIR__.'/../../../../web/uploads/temp/'.$imageName);           
                        $file->move($this->getPath('image'), $imageName);

                        $image = new Image();          
                        $image->setName($imageName);
                        $image->setDescription($imagesDescription[$counter]);
                        $image->setGallery($gallery);
                        $gallery->addImage($image);
                    }
                }
                $counter++;
            }
            $em->persist($image);
        }
    }
    
    /**
     * Retunrs all galleries.
     * @return type
     */
     private function getAllGalleries(){
        $galleries = $this->getDoctrine()
                     ->getRepository($this->repository)
                     ->findBy(array(), array('id' => 'DESC'));
        
        return $galleries;
    }
    
    /**
     * Delete a given gallery id.
     * @param type $id
     * @return type
     */
    public function deleteAction($id){
    
        $em   = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository($this->repository)->find($id);
        $images = $gallery->getImages();
        $videos = $gallery->getVideos();
        
        $path = $this->getPath('image');
        
        foreach($images as $image){
            try{
                $name = $image->getName();
                unlink($path.$name);
                }  catch (\Exception $e){
                }
        }
        
        $pathVideo = $this->getPath('video');
        
        foreach($videos as $video){
            try{
                $name = $video->getName();
                unlink($pathVideo.$name);
                }  catch (\Exception $e){
                }
        }
        
        $em->remove($gallery);
        $em->flush();
        
        $galleries = $this->getAllGalleries();
        
        $this->get('session')->getFlashBag()->add('success','Perfecto! La galeria ha sido eliminada exitosamente.');
        return $this->redirect($this->generateUrl('VorterixBackendBundle_gallery', array('galleries' => $galleries )));
    }
    
    /**
     * Manage gallery edition.
     * @param type $id
     * @return type
     */
     public function editAction($id){
         $em     = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository($this->repository)->find($id);
        $images  = $gallery->getImages();
        $videos  = $gallery->getVideos();

        return $this->render('VorterixBackendBundle:Gallery:edit.html.twig', array('gallery' => $gallery, 'images' => $images, 'videos' => $videos));   
    }
    
    /**
     * Returns gallery files path.
     * @return string
     */
     public function getPath($type){
         
         switch($type){
            
            case 'image': 
                $path = $this->getUploadsDir().'galleries/';
                break;
            case 'video':
                $path = $this->getUploadsDir().'video/cover/';
                break;
        }
                
        return $path;
    }
    
     private function getUploadsDir(){
        return __DIR__.'/../../../../web/uploads/';
    }
   
}
