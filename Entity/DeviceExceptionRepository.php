<?php

namespace Display\PushBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * DeviceExceptionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DeviceExceptionRepository extends EntityRepository
{
    /**
     * @param string $uid
     * @return array
     */
    public function findByDeviceUid($uid)
    {
        $dql = 'SELECT de.id, mt.id AS message_type_id, mt.text AS message_type_text
                FROM DisplayPushBundle:DeviceException de
                JOIN de.device d
                JOIN de.messageType mt
                WHERE d.uid = :uid';

        return $this
            ->getEntityManager()
            ->createQuery($dql)
            ->setParameter('uid', $uid)
            ->getResult()
        ;
    }
}
