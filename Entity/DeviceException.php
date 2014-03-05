<?php

namespace Display\PushBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * DeviceException
 *
 * @ORM\Table("push_device_exception")
 * @ORM\Entity(repositoryClass="Display\PushBundle\Entity\DeviceExceptionRepository")
 */
class DeviceException extends Entity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"DeviceException"})
     */
    private $id;

    /**
     * @var Device
     * @ORM\ManyToOne(targetEntity="Device", inversedBy="exceptions")
     * @ORM\JoinColumn(name="device_id", referencedColumnName="id", onDelete="CASCADE")
     *
     */
    private $device;

    /**
     * @var MessageType
     * @ORM\ManyToOne(targetEntity="MessageType")
     * @ORM\JoinColumn(name="message_type_id", referencedColumnName="id")
     * @Groups({"DeviceException"})
     */
    private $messageType;

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
     * Set device
     *
     * @param Device $device
     * @return DeviceException
     */
    public function setDevice(Device $device = null)
    {
        $this->device = $device;

        return $this;
    }

    /**
     * Get device
     *
     * @return \Display\PushBundle\Entity\Device
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * Set messageType
     *
     * @param MessageType $messageType
     * @return DeviceException
     */
    public function setMessageType(MessageType $messageType = null)
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
