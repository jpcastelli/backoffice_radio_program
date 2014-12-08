<?php

namespace Vorterix\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Image
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class TopImage
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=2000, nullable=true)
     */
    private $description;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="topOrder", type="integer", nullable=true)
     */
    private $topOrder;

    /**
     * @ORM\ManyToOne(targetEntity="Top", inversedBy="topImages")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $top;
    
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
     * Set name
     *
     * @param string $name
     * @return Image
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

    /**
     * Set gallery
     *
     * @param \Vorterix\BackendBundle\Entity\Gallery $gallery
     * @return Image
     */
    public function setGallery(\Vorterix\BackendBundle\Entity\Gallery $gallery = null)
    {
        $this->gallery = $gallery;

        return $this;
    }

    /**
     * Get gallery
     *
     * @return \Vorterix\BackendBundle\Entity\Gallery 
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Image
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
     * Set title
     *
     * @param string $title
     * @return TopImage
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
     * Set top
     *
     * @param \Vorterix\BackendBundle\Entity\Top $top
     * @return TopImage
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
     * Set topOrder
     *
     * @param integer $topOrder
     * @return TopImage
     */
    public function setTopOrder($topOrder)
    {
        $this->topOrder = $topOrder;

        return $this;
    }

    /**
     * Get topOrder
     *
     * @return integer 
     */
    public function getTopOrder()
    {
        return $this->topOrder;
    }
}
