<?php

namespace Display\PushBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Device
 *
 * @ORM\Table("device")
 * @ORM\Entity(repositoryClass="Display\PushBundle\Entity\DeviceRepository")
 */
class Device extends AbstractEntity
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
     * For android it's called device_id is shorter but we can keep both in the same field
     *
     * @var string
     * @ORM\Column(name="uid", type="string", length=40)
     */
    private $uid;

    /**
     * For apple token it's shorter (char 64) but the role is the same
     *
     * @var string
     * @ORM\Column(name="token", type="string", length=255)
     */
    private $token;

    /**
     * @var string
     * @ORM\Column(name="model", type="string", length=128)
     */
    private $model;

    /**
     * @var string $locale
     * @ORM\Column(name="locale", type="string", length=8)
     */
    private $locale;

    /**
     * @var string
     * @ORM\Column(name="app_name", type="string", length=64)
     */
    private $appName;

    /**
     * @var string
     * @ORM\Column(name="app_version", type="string", length=16)
     */
    private $appVersion;

    /**
     * @var string
     * @ORM\Column(name="os_name", type="string", length=32)
     */
    private $osName;
    /**
     * @var string
     * @ORM\Column(name="os_version", type="string", length=16)
     */
    private $osVersion;

    /**
     * active:unistalled
     *
     * @var string
     * @ORM\Column(name="status", type="string", length=16)
     */
    private $status;

    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection $exceptions
     * @ORM\OneToMany(targetEntity="DeviceException", mappedBy="device", cascade={"persist"})
     */
    private $exceptions;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->exceptions = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set uid
     *
     * @param string $uid
     * @return Device
     */
    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * Get uid
     *
     * @return string 
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return Device
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set model
     *
     * @param string $model
     * @return Device
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model
     *
     * @return string 
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @return Device
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string 
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set appName
     *
     * @param string $appName
     * @return Device
     */
    public function setAppName($appName)
    {
        $this->appName = $appName;

        return $this;
    }

    /**
     * Get appName
     *
     * @return string 
     */
    public function getAppName()
    {
        return $this->appName;
    }

    /**
     * Set appVersion
     *
     * @param string $appVersion
     * @return Device
     */
    public function setAppVersion($appVersion)
    {
        $this->appVersion = $appVersion;

        return $this;
    }

    /**
     * Get appVersion
     *
     * @return string 
     */
    public function getAppVersion()
    {
        return $this->appVersion;
    }

    /**
     * Set osName
     *
     * @param string $osName
     * @return Device
     */
    public function setOsName($osName)
    {
        $this->osName = $osName;

        return $this;
    }

    /**
     * Get osName
     *
     * @return string 
     */
    public function getOsName()
    {
        return $this->osName;
    }

    /**
     * Set osVersion
     *
     * @param string $osVersion
     * @return Device
     */
    public function setOsVersion($osVersion)
    {
        $this->osVersion = $osVersion;

        return $this;
    }

    /**
     * Get osVersion
     *
     * @return string 
     */
    public function getOsVersion()
    {
        return $this->osVersion;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Device
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add exceptions
     *
     * @param DeviceException $exceptions
     * @return Device
     */
    public function addException(DeviceException $exceptions)
    {
        $this->exceptions[] = $exceptions;

        return $this;
    }

    /**
     * Remove exceptions
     *
     * @param DeviceException $exceptions
     */
    public function removeException(DeviceException $exceptions)
    {
        $this->exceptions->removeElement($exceptions);
    }

    /**
     * Get exceptions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }
}
