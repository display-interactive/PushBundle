<?php

namespace Display\PushBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * DeviceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DeviceRepository extends EntityRepository
{
    const STATUS_ACTIVE = 'active';
    const STATUS_UNINSTALLED = 'unistalled';

    const OS_ANDROID = 'android';
    const OS_IOS = 'ios';

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return array(
            self::STATUS_ACTIVE,
            self::STATUS_UNINSTALLED
        );
    }

    /**
     * @return array
     */
    public static function getOperatingSystems()
    {
        return array(
            self::OS_ANDROID => self::OS_ANDROID,
            self::OS_IOS => self::OS_IOS
        );
    }

    /**
     * @return array|Device[]
     */
    public function getActives()
    {
        $dql = 'SELECT d, de
                FROM DisplayPushBundle:Device d
                LEFT JOIN d.exceptions de
                WHERE d.status = :status';

        return $this
            ->getEntityManager()
            ->createQuery($dql)
            ->setParameter('status', self::STATUS_ACTIVE)
            ->getResult()
            ;
    }

    /**
     * Get device by Uid
     *
     * @param string $os
     * @param array $applicationIds
     * @param string $locale
     * @param array $uids
     * @return array
     */
    public function findByUids($os = null, $applicationIds = array(), $locale = null, array $uids)
    {
        $qb = $this
            ->createQueryBuilder('d')
            ->addSelect('de')
            ->leftJoin('d.exceptions', 'de')
        ;

        if ($os) {
            $qb
                ->andWhere('d.osName = :os')
                ->setParameter('os', $os)
            ;
        }

        if (count($applicationIds) > 0) {
            $qb
                ->leftJoin('d.application', 'a')
                ->andWhere($qb->expr()->in('a.id', ':application_id'))
                ->setParameter('application_id', $applicationIds)
            ;
        }

        if ($locale) {
            $qb
                ->andWhere('d.locale = :locale')
                ->setParameter('locale', $locale)
            ;
        }

        if (count($uids) > 0) {
            $qb
                ->andWhere($qb->expr()->in('d.uid', ':uids'))
                ->setParameter('uids', $uids)
            ;
        }

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }
}
