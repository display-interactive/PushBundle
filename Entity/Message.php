<?php

namespace Display\PushBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table("push_message")
 * @ORM\Entity(repositoryClass="Display\PushBundle\Entity\MessageRepository")
 */
class Message extends Entity
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
     * @var MessageType
     * @ORM\ManyToOne(targetEntity="MessageType")
     * @ORM\JoinColumn(name="message_type_id", referencedColumnName="id")
     */
    private $messageType;

    /**
     * @var array
     * @ORM\Column(name="translation_data", type="json_array", nullable=true)
     */
    private $translationData;

    /**
     * @var array
     * @ORM\Column(name="custom_data", type="json_array", nullable=true)
     */
    private $customData;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Application")
     * @ORM\JoinTable(name="push_message_application",
     *      joinColumns={@ORM\JoinColumn(name="message_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="application_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private $applications;

    /**
     * @var bool
     * @ORM\Column(name="is_pending", type="boolean")
     */
    private $isPending = true;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->applications = new ArrayCollection();
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
     * Set translationData
     *
     * @param array $translationData
     * @return Message
     */
    public function setTranslationData($translationData)
    {
        $this->translationData = $translationData;

        return $this;
    }

    /**
     * Get translationData
     *
     * @return array
     */
    public function getTranslationData()
    {
        return $this->translationData;
    }


    /**
     * Set customData
     *
     * @param array $customData
     * @return Message
     */
    public function setCustomData($customData)
    {
        $this->customData = $customData;

        return $this;
    }

    /**
     * Get customData
     *
     * @return array
     */
    public function getCustomData()
    {
        return $this->customData;
    }

    /**
     * Set isPending
     *
     * @param boolean $isPending
     * @return Message
     */
    public function setIsPending($isPending)
    {
        $this->isPending = $isPending;

        return $this;
    }

    /**
     * Get isPending
     *
     * @return boolean
     */
    public function getIsPending()
    {
        return $this->isPending;
    }

    /**
     * Set messageType
     *
     * @param MessageType $messageType
     * @return Message
     */
    public function setMessageType(MessageType $messageType = null)
    {
        $this->messageType = $messageType;

        return $this;
    }
    /**
     * Get messageType
     *
     * @return MessageType
     */
    public function getMessageType()
    {
        return $this->messageType;
    }

    /**
     * Add application
     *
     * @param Application $application
     * @return Message
     */
    public function addApplication(Application $application)
    {
        $this->applications[] = $application;

        return $this;
    }

    /**
     * Remove application
     *
     * @param Application $application
     */
    public function removeApplication(Application $application)
    {
        $this->applications->removeElement($application);
    }

    /**
     * Get applications
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getApplications()
    {
        return $this->applications;
    }
}
