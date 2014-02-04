<?php

namespace Display\PushBundle\Controller;

use Display\PushBundle\Entity\Device;
use Display\PushBundle\Entity\DeviceException;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Routing\ClassResourceInterface;
use JMS\Serializer\SerializationContext;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use Display\PushBundle\Entity\DeviceRepository;

class DeviceController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Create and update a device subscription
     *
     * @RequestParam(name="uid", requirements="[a-zA-Z0-9-]+", description="Device Uid or Id uid: 54a9d410ea6539d8797c62c5f8c95cb551eb99cc, id: 27507c9de8c78b3f", nullable=false)
     * @RequestParam(name="token", description="Device Token", nullable=false)
     * @RequestParam(name="model", description="Device model ex: iPhone", nullable=false)
     * @RequestParam(name="locale", requirements="[a-zA-Z0-9_]+", description="Locale of the app ex: fr_FR , en, es_ES", nullable=false)
     * @RequestParam(name="app_name", description="The App Name ex: Disney Web Radio", nullable=false)
     * @RequestParam(name="app_version", description="The application version ex: 1.0", nullable=false)
     * @RequestParam(name="os_name", requirements="(android|ios)", description="The operating system name ex: android", nullable=false)
     * @RequestParam(name="os_version", description="The operating system version ex: 4.4.1", nullable=false)
     */
    public function postAction(ParamFetcher $paramFetcher)
    {
        /** @var ObjectManager $em */
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('DisplayPushBundle:Device');
        $device = $repository->findOneBy(array('uid' => $paramFetcher->get('uid')));

        if (!$device) {
            $device = new Device();
        }

        $device
            ->setUid($paramFetcher->get('uid'))
            ->setToken($paramFetcher->get('token'))
            ->setModel($paramFetcher->get('model'))
            ->setLocale($paramFetcher->get('locale'))
            ->setAppName($paramFetcher->get('app_name'))
            ->setAppVersion($paramFetcher->get('app_version'))
            ->setOsName($paramFetcher->get('os_name'))
            ->setOsVersion($paramFetcher->get('os_version'))
            ->setStatus(DeviceRepository::STATUS_ACTIVE)
        ;

        $em->persist($device);
        $em->flush();

        return $this->handleView($this->view(array('success' => true), 200));
    }

    /**
     * Add a device exception
     * post_device_exceptions > [POST] /devices/{slug}/exceptions
     *
     * @RequestParam(name="uid", requirements="[a-zA-Z0-9-]+", description="Device Uid or Id uid: 54a9d410ea6539d8797c62c5f8c95cb551eb99cc, id: 27507c9de8c78b3f", nullable=false)
     * @RequestParam(name="message_type_id", requirements="\d+", description="The message type id", nullable=false)
     */
    public function postExceptionsAction(ParamFetcher $paramFetcher)
    {
        /** @var ObjectManager $em */
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('DisplayPushBundle:Device');
        $device = $repository->findOneBy(array('uid' => $paramFetcher->get('uid')));
        $type = $em->find('DisplayPushBundle:MessageType', $paramFetcher->get('message_type_id'));


        $success = false;
        if ($device && $type) {
            $exception = new DeviceException();
            $exception
                ->setDevice($device)
                ->setMessageType($type)
            ;

            $em->persist($exception);
            $em->flush();
            $success = true;
        }

        return $this->handleView($this->view(compact('success'), 200));
    }

    /**
     * Get device exceptions for a given phone
     * get_device_exceptions > [GET] /devices/{slug}/exceptions
     *
     * @param mixed $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getExceptionsAction($slug)
    {
        /** @var ObjectManager $em */
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('DisplayPushBundle:DeviceException');

        return $this->handleView($this->view($repository->findByDeviceUid($slug), 200));
    }

    /**
     * Delete a device exception
     * delete_device_exceptions > [DELETE] /devices/{id}/exceptions
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteExceptionAction($id)
    {
        /** @var ObjectManager $em */
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('DisplayPushBundle:DeviceException');
        $exception = $repository->find($id);

        $success = false;
        if ($exception) {
            $em->remove($exception);
            $em->flush();
            $success = true;
        }

        return $this->handleView($this->view(compact('success'), 200));
    }
} 