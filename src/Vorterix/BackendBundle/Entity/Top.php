<?php

namespace Vorterix\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Top
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Top
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
     * @ORM\OneToMany(targetEntity="TopImage", mappedBy="top", cascade={"persist"})
     */
    protected $topImages;


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
     * @return Top
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
     * Constructor
     */
    public function __construct()
    {
        $this->topImages = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add topImages
     *
     * @param \Vorterix\BackendBundle\Entity\TopImage $topImages
     * @return Top
     */
    public function addTopImage(\Vorterix\BackendBundle\Entity\TopImage $topImages)
    {
        $this->topImages[] = $topImages;

        return $this;
    }

    /**
     * Remove topImages
     *
     * @param \Vorterix\BackendBundle\Entity\TopImage $topImages
     */
    public function removeTopImage(\Vorterix\BackendBundle\Entity\TopImage $topImages)
    {
        $this->topImages->removeElement($topImages);
    }

    /**
     * Get topImages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTopImages()
    {
        return $this->topImages;
    }
}
