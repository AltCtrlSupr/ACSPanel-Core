<?php
/**
 * HttpdAliasRepository
 *
 * @author genar
 */
namespace ACS\ACSPanelBundle\Model;

use Doctrine\ORM\EntityRepository;
use ACS\ACSPanelUsersBundle\Entity\FosUser;
use ACS\ACSPanelUsersBundle\Doctrine\AclEntityRepository;


class DomainRepository extends AclEntityRepository
{
    private $acl_filter;

    public function setAclFilter($acl_filter)
    {
        $this->acl_filter = $acl_filter;
    }

    public function getUserViewable($user)
    {
		$entities_raw = $this->_em->createQuery('SELECT d FROM ACS\ACSPanelBundle\Entity\Domain d');
		$entities = $this->acl_filter->apply($entities_raw, ['VIEW'], $user, 'd')->getResult();

        return $entities;
    }

    /**
     * @deprecated
     */
    public function findByUser(FosUser $user)
    {
        $query = $this->_em->createQuery('SELECT d FROM ACS\ACSPanelBundle\Entity\Domain d WHERE d.user = ?1')->setParameter(1, $user->getId());
        return $query->getResult();
    }

    /**
     * @deprecated
     */
    public function findByUsers(Array $ids)
    {
        $query = $this->_em->createQuery('SELECT d FROM ACS\ACSPanelBundle\Entity\Domain d WHERE d.user IN (?1)')->setParameter(1, $ids);
        return $query->getResult();
    }


    /**
     * Return the domains that are aliases
     *
     * @return Collection
     */
    public function findAliases()
    {
        $query = $this->_em->createQuery('SELECT d FROM ACS\ACSPanelBundle\Entity\Domain d WHERE d.is_httpd_alias = true');
        return $query->getResult();
    }

    /**
     * Return the domains that are aliases for a specific user
     *
     */
    public function findAliasesByUser($user)
    {
        $query = $this->_em->createQuery('SELECT d FROM ACS\ACSPanelBundle\Entity\Domain d WHERE d.is_httpd_alias = true AND d.user_id = ?1 ')->setParameter(1, $user->getId());
        return $query->getResult();
    }

}

?>
