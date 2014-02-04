<?php

namespace Display\PushBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sending
 *
 * @ORM\Table("sending")
 * @ORM\Entity(repositoryClass="Display\PushBundle\Entity\SendingRepository")
 */
class Sending extends AbstractEntity
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
     * @var Device
     * @ORM\ManyToOne(targetEntity="Device")
     * @ORM\JoinColumn(name="device_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $device;

    /**
     * @var Message
     * @ORM\ManyToOne(targetEntity="Message")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id")
     */
    private $message;

    /**
     * queued:delivered:failed
     * @var string
     * @ORM\Column(name="status", type="string", length=16)
     */
    private $status;

    /**
     * @var \DateTime
     * @ORM\Column(name="delivered_at", type="datetime", nullable=true)
     */
    private $deliveredAt;

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
     * Set status
     *
     * @param string $status
     * @return Sending
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
     * Set deliveredAt
     *
     * @param \DateTime $deliveredAt
     * @return Sending
     */
    public function setDeliveredAt($deliveredAt)
    {
        $this->deliveredAt = $deliveredAt;

        return $this;
    }

    /**
     * Get deliveredAt
     *
     * @return \DateTime 
     */
    public function getDeliveredAt()
    {
        return $this->deliveredAt;
    }

    /**
     * Set device
     *
     * @param Device $device
     * @return Sending
     */
    public function setDevice(Device $device = null)
    {
        $this->device = $device;

        return $this;
    }

    /**
     * Get device
     *
     * @return Device
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * Set message
     *
     * @param Message $message
     * @return Sending
     */
    public function setMessage(Message $message = null)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return \Display\PushBundle\Entity\Message 
     */
    public function getMessage()
    {
        return $this->message;
    }
}
