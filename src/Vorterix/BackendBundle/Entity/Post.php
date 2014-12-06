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
     * @ORM\ManyToOne(targetEntity="Category")
     */
    private $category;
    
    /**
     * @ORM\ManyToOne(targetEntity="Section")
     */
    private $section;
    
    /**
     * @ORM\ManyToMany(targetEntity="Tag")
     * @ORM\JoinTable(name="posts_tags")
     */
    private $tags;
    
    /**
     * @ORM\ManyToMany(targetEntity="Gallery")
     * @ORM\JoinTable(name="posts_galleries")
     */
    private $galleries;
    
    /**
     * @ORM\ManyToOne(targetEntity="Top")
     */
    private $top;
    
    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @var boolean
     *
     * @ORM\Column(name="comments", type="boolean")
     */
    private $comments;
    
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


    /**
     * Set category
     *
     * @param \Vorterix\BackendBundle\Entity\Category $category
     * @return Post
     */
    public function setCategory(\Vorterix\BackendBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Vorterix\BackendBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
        $this->galleries = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add tags
     *
     * @param \Vorterix\BackendBundle\Entity\Tag $tags
     * @return Post
     */
    public function addTag(\Vorterix\BackendBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;

        return $this;
    }

    /**
     * Remove tags
     *
     * @param \Vorterix\BackendBundle\Entity\Tag $tags
     */
    public function removeTag(\Vorterix\BackendBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Add galleries
     *
     * @param \Vorterix\BackendBundle\Entity\Gallery $galleries
     * @return Post
     */
    public function addGallery(\Vorterix\BackendBundle\Entity\Gallery $galleries)
    {
        $this->galleries[] = $galleries;

        return $this;
    }

    /**
     * Remove galleries
     *
     * @param \Vorterix\BackendBundle\Entity\Gallery $galleries
     */
    public function removeGallery(\Vorterix\BackendBundle\Entity\Gallery $galleries)
    {
        $this->galleries->removeElement($galleries);
    }

    /**
     * Get galleries
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGalleries()
    {
        return $this->galleries;
    }

    /**
     * Set comments
     *
     * @param boolean $comments
     * @return Post
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments
     *
     * @return boolean 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set section
     *
     * @param \Vorterix\BackendBundle\Entity\Section $section
     * @return Post
     */
    public function setSection(\Vorterix\BackendBundle\Entity\Section $section = null)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * Get section
     *
     * @return \Vorterix\BackendBundle\Entity\Section 
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set top
     *
     * @param \Vorterix\BackendBundle\Entity\Top $top
     * @return Post
     */
    public function setTop(\Vorterix\BackendBundle\Entity\Top $top = null)
    {
        $this->top = $top;

        return $this;
    }

    /**
     * Get top
     *
     * @return \Vorterix\BackendBundle\Entity\Top 
     */
    public function getTop()
    {
        return $this->top;
    }

    /**
     * Set user
     *
     * @param \Vorterix\BackendBundle\Entity\User $user
     * @return Post
     */
    public function setUser(\Vorterix\BackendBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Vorterix\BackendBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
