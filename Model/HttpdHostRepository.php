<?php
/**
 * HttpdAliasRepository
 *
 * @author genar
 */
namespace ACS\ACSPanelBundle\Model;

use Doctrine\ORM\EntityRepository;

use ACS\ACSPanelUsersBundle\Entity\FosUser;

class HttpdHostRepository extends EntityRepository
{
    private $acl_filter;

    public function setAclFilter($acl_filter)
    {
        $this->acl_filter = $acl_filter;
    }

    public function findByUser(FosUser $user)
    {
        $query = $this->_em->createQuery('SELECT h FROM ACS\ACSPanelBundle\Entity\HttpdHost h INNER JOIN h.domain d WHERE d.user = ?1')->setParameter(1, $user->getId());
        return $query->getResult();
    }

    public function findByUsers(Array $user)
    {
        $query = $this->_em->createQuery('SELECT h FROM ACS\ACSPanelBundle\Entity\HttpdHost h INNER JOIN h.domain d WHERE d.user IN (?1)')->setParameter(1, $user);
        return $query->getResult();
    }

    public function getUserViewable($user)
    {
		$entities_raw = $this->_em->createQuery('SELECT h,d,pd FROM ACS\ACSPanelBundle\Entity\HttpdHost h INNER JOIN h.domain d INNER JOIN d.parent_domain pd');
		$entities = $this->acl_filter->apply($entities_raw, ['VIEW'], $user, 'h')->getResult();

        return $entities;
    }

}
