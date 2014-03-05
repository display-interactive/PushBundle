<?php

namespace Display\PushBundle\Entity;

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
     * @var bool
     * @ORM\Column(name="is_pending", type="boolean")
     */
    private $isPending = true;

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
     * @param \Display\PushBundle\Entity\MessageType $messageType
     * @return Message
     */
    public function setMessageType(\Display\PushBundle\Entity\MessageType $messageType = null)
    {
        $this->messageType = $messageType;

        return $this;
    }

    /**
     * Get messageType
     *
     * @return \Display\PushBundle\Entity\MessageType 
     */
    public function getMessageType()
    {
        return $this->messageType;
    }
}
