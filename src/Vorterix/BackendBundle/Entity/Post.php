<?php

namespace Vorterix\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Post
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Post
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="pretitle", type="string", length=255)
     */
    private $pretitle;

    /**
     * @var string
     *
     * @ORM\Column(name="shortDescription", type="string", length=255)
     */
    private $shortDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="cover", type="string", length=255)
     */
    private $cover;

    /**
     * @var string
     *
     * @ORM\Column(name="main_video", type="string", length=255)
     */
    private $mainVideo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createD", type="datetime")
     */
    private $createD;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="publishD", type="datetime")
     */
    private $publishD;


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
     * Set title
     *
     * @param string $title
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set pretitle
     *
     * @param string $pretitle
     * @return Post
     */
    public function setPretitle($pretitle)
    {
        $this->pretitle = $pretitle;

        return $this;
    }

    /**
     * Get pretitle
     *
     * @return string 
     */
    public function getPretitle()
    {
        return $this->pretitle;
    }

    /**
     * Set shortDescription
     *
     * @param string $shortDescription
     * @return Post
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * Get shortDescription
     *
     * @return string 
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Post
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set cover
     *
     * @param string $cover
     * @return Post
     */
    public function setCover($cover)
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * Get cover
     *
     * @return string 
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * Set mainVideo
     *
     * @param string $mainVideo
     * @return Post
     */
    public function setMainVideo($mainVideo)
    {
        $this->mainVideo = $mainVideo;

        return $this;
    }

    /**
     * Get mainVideo
     *
     * @return string 
     */
    public function getMainVideo()
    {
        return $this->mainVideo;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return Post
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createD
     *
     * @param \DateTime $createD
     * @return Post
     */
    public function setCreateD($createD)
    {
        $this->createD = $createD;

        return $this;
    }

    /**
     * Get createD
     *
     * @return \DateTime 
     */
    public function getCreateD()
    {
        return $this->createD;
    }

    /**
     * Set publishD
     *
     * @param \DateTime $publishD
     * @return Post
     */
    public function setPublishD($publishD)
    {
        $this->publishD = $publishD;

        return $this;
    }

    /**
     * Get publishD
     *
     * @return \DateTime 
     */
    public function getPublishD()
    {
        return $this->publishD;
    }
}
