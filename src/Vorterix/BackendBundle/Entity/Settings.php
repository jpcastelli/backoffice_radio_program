<?php

namespace Vorterix\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Settings
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Settings
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
     * @ORM\Column(name="keySetting", type="string", length=255)
     */
    private $keySetting;

    /**
     * @var string
     *
     * @ORM\Column(name="ValueSetting", type="string", length=255)
     */
    private $valueSetting;


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
     * Set keySetting
     *
     * @param string $keySetting
     * @return Settings
     */
    public function setKeySetting($keySetting)
    {
        $this->keySetting = $keySetting;

        return $this;
    }

    /**
     * Get keySetting
     *
     * @return string 
     */
    public function getKeySetting()
    {
        return $this->keySetting;
    }

    /**
     * Set valueSetting
     *
     * @param string $valueSetting
     * @return Settings
     */
    public function setValueSetting($valueSetting)
    {
        $this->valueSetting = $valueSetting;

        return $this;
    }

    /**
     * Get valueSetting
     *
     * @return string 
     */
    public function getValueSetting()
    {
        return $this->valueSetting;
    }
}
