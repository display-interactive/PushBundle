<?php

namespace Display\PushBundle\Controller;

use Display\PushBundle\Entity\Application;
use Display\PushBundle\Entity\Device;
use Display\PushBundle\Entity\DeviceException;
use Display\PushBundle\Entity\MessageType;
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
        try {
            /** @var ObjectManager $em */
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository('DisplayPushBundle:Device');
            $device = $repository->findOneBy(array('uid' => $paramFetcher->get('uid')));

            if (!$device) {
                $device = $repository->findOneBy(array('token' => $paramFetcher->get('token')));
                if (!$device) {
                    $device = new Device();
                }
            }

            $application = $em->getRepository('DisplayPushBundle:Application')->findOneBy(array(
                'name' => $paramFetcher->get('app_name'),
                'version' => $paramFetcher->get('app_version')
            ));

            if (!$application) {
                $application = new Application();
            }
            $device->setApplication($application);

            $device
                ->setUid($paramFetcher->get('uid'))
                ->setToken($paramFetcher->get('token'))
                ->setModel($paramFetcher->get('model'))
                ->setLocale($paramFetcher->get('locale'))
                ->setOsName($paramFetcher->get('os_name'))
                ->setOsVersion($paramFetcher->get('os_version'))
                ->setStatus(DeviceRepository::STATUS_ACTIVE)
            ;

            $application
                ->setName($paramFetcher->get('app_name'))
                ->setVersion($paramFetcher->get('app_version'))
            ;

            $em->persist($device);
            $em->flush();
            $success = true;
        } catch (\Exception $e) {
            $success = false;
        }

        return $this->handleView($this->view(compact('success'), 200));
    }

    /**
     * Add a device exception
     * post_device_exceptions > [POST] /devices/{uid}/exceptions
     *
     * @RequestParam(name="ids", requirements="[0-9,]+", description="The message type id", nullable=false)
     */
    public function postExceptionsAction(ParamFetcher $paramFetcher, $uid)
    {
        try {
            /** @var ObjectManager $em */
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository('DisplayPushBundle:Device');
            $device = $repository->findOneBy(array('uid' => $uid));
            $ids = explode(',', $paramFetcher->get('ids'));

            $types = array();
            foreach ($ids as $id) {
                $types[] = $em->find('DisplayPushBundle:MessageType', $id);
            }

            $success = false;
            if ($device && count($types)) {
                $exceptions = $device->getExceptions();
                /** @var MessageType $type */
                foreach ($types as $type) {
                    $found = false;
                    /** @var DeviceException $exception */
                    foreach ($exceptions as $exception) {
                        if ($exception->getMessageType()->getId() == $type->getId()) {
                            $found = true;
                        }
                    }

                    if (!$found) {
                        $exception = new DeviceException();
                        $exception
                            ->setDevice($device)
                            ->setMessageType($type)
                        ;

                        $em->persist($exception);
                        $success = true;
                    }
                }
                $em->flush();
            }
        } catch (\Exception $e) {
            $success = false;
        }

        return $this->handleView($this->view(compact('success'), 200));
    }

    /**
     * Get device exceptions for a given phone
     * get_device_exceptions > [GET] /devices/{slug}/exceptions
     *
     * @param mixed $uid
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getExceptionsAction($uid)
    {
        /** @var ObjectManager $em */
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('DisplayPushBundle:DeviceException');

        return $this->handleView($this->view($repository->findByDeviceUid($uid), 200));
    }

    /**
     * Delete a device exception
     * delete_device_exceptions > [DELETE] /devices/{uid}/exceptions/{ids}
     *
     * @param string $uid
     * @param mixed $ids
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteExceptionAction($uid, $ids)
    {
        try {
            /** @var ObjectManager $em */
            $em = $this->getDoctrine()->getManager();

            $repository = $em->getRepository('DisplayPushBundle:Device');
            $device = $repository->findOneBy(array('uid' => $uid));

            $ids = explode(',', $ids);
            $types = array();
            $repository = $em->getRepository('DisplayPushBundle:MessageType');
            foreach ($ids as $id) {
                $types[] = $repository->find($id);
            }

            $success = false;
            if ($device && count($types)) {
                foreach ($types as $messageType) {
                    $exception = $em->getRepository('DisplayPushBundle:DeviceException')->findOneBy(compact('device', 'messageType'));
                    if ($exception) {
                        $em->remove($exception);
                        $success = true;
                    }
                }

                $em->flush();
            }
        } catch (\Exception $e) {
            $success = false;
        }

        return $this->handleView($this->view(compact('success'), 200));
    }
} 