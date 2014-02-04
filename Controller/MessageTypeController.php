<?php

namespace Display\PushBundle\Controller;

use Display\PushBundle\Entity\Device;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Routing\ClassResourceInterface;
use JMS\Serializer\SerializationContext;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use Display\PushBundle\Entity\DeviceRepository;

class MessageTypeController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Get all message type
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cgetAction()
    {
        /** @var ObjectManager $em */
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('DisplayPushBundle:MessageType');
        $message_types = $repository->findAll();

        $view = $this->view($message_types, 200)
            ->setSerializationContext(SerializationContext::create()->setGroups(array('MessageType')));

        return $this->handleView($view);
    }
}