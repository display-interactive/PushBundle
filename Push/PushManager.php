<?php
namespace Display\PushBundle\Push;

use Display\PushBundle\Entity\Device;
use Display\PushBundle\Entity\DeviceException;
use Display\PushBundle\Entity\DeviceRepository;
use Display\PushBundle\Entity\Message;
use Display\PushBundle\Entity\MessageType;
use Display\PushBundle\Entity\Sending;
use Display\PushBundle\Entity\SendingRepository;
use RMS\PushNotificationsBundle\Device\iOS\Feedback;
use RMS\PushNotificationsBundle\Message\AndroidMessage;
use RMS\PushNotificationsBundle\Message\iOSMessage;
use RMS\PushNotificationsBundle\Message\MessageInterface;
use Symfony\Component\DependencyInjection\Container;

class PushManager
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $manager;

    /**
     * @var string
     */
    private $translationDomain;

    /**
     * @var \RMS\PushNotificationsBundle\Service\Notifications
     */
    private $pusher;

    /**
     * @param Container $container
     * @param string $name
     * @param string $translationDomain
     */
    public function __construct(Container $container, $name, $translationDomain = null)
    {
        $this->container = $container;
        $this->manager = $container->get('doctrine')->getManager($name);
        $this->translationDomain = $translationDomain;
        $this->pusher = $container->get('rms_push_notifications');
    }

    /**
     * Add push message
     *
     * @param string $text text or translation key
     * @param string $data translation data
     * @return Message
     */
    public function addMessage($text, $data = null)
    {
        $message = new Message();
        $message
            ->setMessageType($this->addMessageType($text))
            ->setTranslationData($data)
        ;

        $this->manager->persist($message);
        $this->manager->flush();

        return $message;
    }
    /**
     * Send pending messages
     */
    public function sendPendingMessages()
    {
        $devices = $this->getActiveDevices();

        /** @var Message $message */
        foreach ($this->getPendingMessages() as $message) {
            /** @var Device $device */
            foreach ($devices as $device) {
                $this->send($device, $message);
            }
            $message->setIsPending(false);
        }
        $this->manager->flush();

        $this->checkFeedback();
    }

    /**
     * Send a specific message to a specific device
     *
     * @param string $text
     * @param string $locale
     * @param string $os
     * @param array $uids
     */
    public function sendMessage($text, $os = null, $locale = null, $uids = array())
    {
        $message = $this->addMessage($text);
        //Get devices by uid and locale
        $devices = $this->manager->getRepository('DisplayPushBundle:Device')->findByUids($os, $locale, $uids);

        /** @var Device $device */
        foreach ($devices as $device) {
            $this->send($device, $message);
        }
        $message->setIsPending(false);

        $this->manager->flush();

        $this->checkFeedback();
    }

    /**
     * check feedback (works only for iOS)
     */
    public function checkFeedback()
    {
        $uuids = $this->container->get("rms_push_notifications.ios.feedback")->getDeviceUUIDs();
        $this->container->get('logger')->addDebug(json_encode($uuids));
        foreach ($this->getActiveDevices() as $device) {
            /** @var Feedback $uid */
            foreach ($uuids as $uid) {
                if ($device->getToken() === $uid->uuid
                ) {
                    $device->setStatus(DeviceRepository::STATUS_UNINSTALLED);
                    $this->manager->persist($device);
                }
            }
        }

        $this->manager->flush();
    }

    /**
     * Send a message to a device
     *
     * @param Device $device
     * @param Message $message
     * @return bool
     */
    protected function send(Device $device, Message $message)
    {
        if ($this->hasException($device, $message)) return null;

        $this->container->get('logger')->addDebug(sprintf('sending to %s device: %s', $device->getOsName(), $device->getId()));
        $sending = new Sending();
        $sending
            ->setStatus(SendingRepository::STATUS_QUEUED)
            ->setDevice($device)
            ->setMessage($message)
        ;
        $this->manager->persist($sending);

        $push = $this->getPushMessage($device, $message);
        //$push is null if device is not iOS|Android && push succeed
        if (null !== $push && $this->pusher->send($push)) {
            $sending->setStatus(SendingRepository::STATUS_DELIVERED); ;
            $sending->setDeliveredAt(new \DateTime());
            $success = true;
        } else {
            $sending->setStatus(SendingRepository::STATUS_FAILED); ;
            $success = false;
        }

        return $success;
    }


    /**
     * Add and return message type
     *
     * @param string $text
     * @return MessageType
     */
    protected function addMessageType($text)
    {
        $type = $this->manager->getRepository('DisplayPushBundle:MessageType')->findOneBy(array('text' => $text));
        if ($type) return $type;

        $type = new MessageType();
        $type->setText($text);

        $this->manager->persist($type);
        $this->manager->flush();

        return $type;
    }

    /**
     * Get pending message
     *
     * @return array|\Display\PushBundle\Entity\Message[]
     */
    protected function getPendingMessages()
    {
        return $this->manager->getRepository('DisplayPushBundle:Message')->getPendings();
    }

    /**
     * Get active device
     *
     * @return array|\Display\PushBundle\Entity\Device[]
     */
    protected function getActiveDevices()
    {
        return $this->manager->getRepository('DisplayPushBundle:Device')->getActives();
    }

    /**
     * Get push message for given device
     *
     * @param Device $device
     * @param Message $message
     * @return MessageInterface
     */
    protected function getPushMessage(Device $device, Message $message)
    {
        $translator = $this->container->get('translator');
        switch ($device->getOsName()) {
            case DeviceRepository::OS_IOS:
                $push = new iOSMessage();
                $push->setAPSSound('default');
                $push->setAPSBadge('1');
                $push->setAPSContentAvailable(1);
                break;
            case DeviceRepository::OS_ANDROID:
                $push = new AndroidMessage();
                $push->setGCM(true);
                $push->setData(array('id' => $message->getId()));
                break;
            default:
                return null;
                break;
        }
        $push->setDeviceIdentifier($device->getToken());

        $text = $translator->trans(
            $message->getMessageType()->getText(),
            $message->getTranslationData() !== null ? $message->getTranslationData() : array(),
            $this->translationDomain,
            $device->getLocale()
        );
        $push->setMessage($text);

        return $push;
    }

    /**
     * Check if device has exception on this type of message
     *
     * @param Device $device
     * @param Message $message
     * @return bool
     */
    protected function hasException(Device $device, Message $message)
    {
        if (0 === $device->getExceptions()->count()) return false;

        /** @var DeviceException $exception */
        foreach ($device->getExceptions() as $exception) {
            if ($exception->getMessageType()->getText() == $message->getMessageType()->getText()) {
                return true;
            }
        }

        return false;
    }
}