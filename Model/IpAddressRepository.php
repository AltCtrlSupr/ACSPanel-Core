<?php
/**
 * ServiceTypeRepository
 *
 * @author genar
 */
namespace ACS\ACSPanelBundle\Model;

use ACS\ACSPanelUsersBundle\Doctrine\AclEntityRepository;

class IpAddressRepository extends AclEntityRepository
{
    public function getUserViewable($user)
    {
		$entities_raw = $this->_em->createQuery('SELECT ip FROM ACS\ACSPanelBundle\Entity\IpAddress ip');
		$entities = $this->getAclFilter()->apply($entities_raw, ['VIEW'], $user, 'ip')->getResult();

        return $entities;
    }


}
