<?php

namespace Vorterix\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Highlight
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Highlight
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
     * @ORM\Column(name="link", type="string", length=255)
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var integer
     *
     * @ORM\Column(name="columns", type="integer", nullable=true)
     */
    private $columns;

    /**
     * @var string
     *
     * @ORM\Column(name="little_image", type="string", length=255, nullable=true)
     */
    private $littleImage;

    /**
     * @var string
     *
     * @ORM\Column(name="big_image", type="string", length=255, nullable=true)
     */
    private $bigImage;


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
     * Set link
     *
     * @param string $link
     * @return Highlight
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Highlight
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
     * Set columns
     *
     * @param integer $columns
     * @return Highlight
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Get columns
     *
     * @return integer 
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Set littleImage
     *
     * @param string $littleImage
     * @return Highlight
     */
    public function setLittleImage($littleImage)
    {
        $this->littleImage = $littleImage;

        return $this;
    }

    /**
     * Get littleImage
     *
     * @return string 
     */
    public function getLittleImage()
    {
        return $this->littleImage;
    }

    /**
     * Set bigImage
     *
     * @param string $bigImage
     * @return Highlight
     */
    public function setBigImage($bigImage)
    {
        $this->bigImage = $bigImage;

        return $this;
    }

    /**
     * Get bigImage
     *
     * @return string 
     */
    public function getBigImage()
    {
        return $this->bigImage;
    }
}
