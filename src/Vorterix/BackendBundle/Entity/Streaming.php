<?php

namespace Vorterix\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Streaming
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Streaming
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="background", type="string", length=255, nullable=true)
     */
    private $background;

    /**
     * @var string
     *
     * @ORM\Column(name="imagen", type="string", length=255, nullable=true)
     */
    private $imagen;

    /**
     * @var string
     *
     * @ORM\Column(name="mainStreaming", type="string", length=255)
     */
    private $mainStreaming;

    /**
     * @var string
     *
     * @ORM\Column(name="hashtag", type="string", length=255, nullable=true)
     */
    private $hashtag;

    /**
     * @var string
     *
     * @ORM\Column(name="twFeed", type="string", length=255, nullable=true)
     */
    private $twFeed;

    /**
     * @var string
     *
     * @ORM\Column(name="streamCam1", type="string", length=255, nullable=true)
     */
    private $streamCam1;

    /**
     * @var string
     *
     * @ORM\Column(name="nameCam1", type="string", length=255, nullable=true)
     */
    private $nameCam1;

    /**
     * @var string
     *
     * @ORM\Column(name="streamCam2", type="string", length=255, nullable=true)
     */
    private $streamCam2;

    /**
     * @var string
     *
     * @ORM\Column(name="nameCam2", type="string", length=255, nullable=true)
     */
    private $nameCam2;

    /**
     * @var string
     *
     * @ORM\Column(name="streamCam3", type="string", length=255, nullable=true)
     */
    private $streamCam3;

    /**
     * @var string
     *
     * @ORM\Column(name="nameCam3", type="string", length=255, nullable=true)
     */
    private $nameCam3;

    /**
     * @var string
     *
     * @ORM\Column(name="streamCam4", type="string", length=255, nullable=true)
     */
    private $streamCam4;

    /**
     * @var string
     *
     * @ORM\Column(name="nameCam4", type="string", length=255, nullable=true)
     */
    private $nameCam4;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set background
     *
     * @param string $background
     * @return Streaming
     */
    public function setBackground($background)
    {
        $this->background = $background;

        return $this;
    }

    /**
     * Get background
     *
     * @return string 
     */
    public function getBackground()
    {
        return $this->background;
    }

    /**
     * Set imagen
     *
     * @param string $imagen
     * @return Streaming
     */
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;

        return $this;
    }

    /**
     * Get imagen
     *
     * @return string 
     */
    public function getImagen()
    {
        return $this->imagen;
    }

    /**
     * Set mainStreaming
     *
     * @param string $mainStreaming
     * @return Streaming
     */
    public function setMainStreaming($mainStreaming)
    {
        $this->mainStreaming = $mainStreaming;

        return $this;
    }

    /**
     * Get mainStreaming
     *
     * @return string 
     */
    public function getMainStreaming()
    {
        return $this->mainStreaming;
    }

    /**
     * Set hashtag
     *
     * @param string $hashtag
     * @return Streaming
     */
    public function setHashtag($hashtag)
    {
        $this->hashtag = $hashtag;

        return $this;
    }

    /**
     * Get hashtag
     *
     * @return string 
     */
    public function getHashtag()
    {
        return $this->hashtag;
    }

    /**
     * Set twFeed
     *
     * @param string $twFeed
     * @return Streaming
     */
    public function setTwFeed($twFeed)
    {
        $this->twFeed = $twFeed;

        return $this;
    }

    /**
     * Get twFeed
     *
     * @return string 
     */
    public function getTwFeed()
    {
        return $this->twFeed;
    }

    /**
     * Set streamCam1
     *
     * @param string $streamCam1
     * @return Streaming
     */
    public function setStreamCam1($streamCam1)
    {
        $this->streamCam1 = $streamCam1;

        return $this;
    }

    /**
     * Get streamCam1
     *
     * @return string 
     */
    public function getStreamCam1()
    {
        return $this->streamCam1;
    }

    /**
     * Set nameCam1
     *
     * @param string $nameCam1
     * @return Streaming
     */
    public function setNameCam1($nameCam1)
    {
        $this->nameCam1 = $nameCam1;

        return $this;
    }

    /**
     * Get nameCam1
     *
     * @return string 
     */
    public function getNameCam1()
    {
        return $this->nameCam1;
    }

    /**
     * Set streamCam2
     *
     * @param string $streamCam2
     * @return Streaming
     */
    public function setStreamCam2($streamCam2)
    {
        $this->streamCam2 = $streamCam2;

        return $this;
    }

    /**
     * Get streamCam2
     *
     * @return string 
     */
    public function getStreamCam2()
    {
        return $this->streamCam2;
    }

    /**
     * Set nameCam2
     *
     * @param string $nameCam2
     * @return Streaming
     */
    public function setNameCam2($nameCam2)
    {
        $this->nameCam2 = $nameCam2;

        return $this;
    }

    /**
     * Get nameCam2
     *
     * @return string 
     */
    public function getNameCam2()
    {
        return $this->nameCam2;
    }

    /**
     * Set streamCam3
     *
     * @param string $streamCam3
     * @return Streaming
     */
    public function setStreamCam3($streamCam3)
    {
        $this->streamCam3 = $streamCam3;

        return $this;
    }

    /**
     * Get streamCam3
     *
     * @return string 
     */
    public function getStreamCam3()
    {
        return $this->streamCam3;
    }

    /**
     * Set nameCam3
     *
     * @param string $nameCam3
     * @return Streaming
     */
    public function setNameCam3($nameCam3)
    {
        $this->nameCam3 = $nameCam3;

        return $this;
    }

    /**
     * Get nameCam3
     *
     * @return string 
     */
    public function getNameCam3()
    {
        return $this->nameCam3;
    }

    /**
     * Set streamCam4
     *
     * @param string $streamCam4
     * @return Streaming
     */
    public function setStreamCam4($streamCam4)
    {
        $this->streamCam4 = $streamCam4;

        return $this;
    }

    /**
     * Get streamCam4
     *
     * @return string 
     */
    public function getStreamCam4()
    {
        return $this->streamCam4;
    }

    /**
     * Set nameCam4
     *
     * @param string $nameCam4
     * @return Streaming
     */
    public function setNameCam4($nameCam4)
    {
        $this->nameCam4 = $nameCam4;

        return $this;
    }

    /**
     * Get nameCam4
     *
     * @return string 
     */
    public function getNameCam4()
    {
        return $this->nameCam4;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Streaming
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
}
