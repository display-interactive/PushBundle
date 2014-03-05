<?php

namespace Display\PushBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * MessageType
 *
 * @ORM\Table("push_message_type")
 * @ORM\Entity(repositoryClass="Display\PushBundle\Entity\MessageTypeRepository")
 */
class MessageType extends Entity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"MessageType"})
     */
    private $id;

    /**
     * Could be text or translation key
     *
     * @var string
     * @ORM\Column(name="text", type="text")
     * @Groups({"MessageType", "DeviceException"})
     */
    private $text;

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
     * Set text
     *
     * @param string $text
     * @return MessageType
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }
}
