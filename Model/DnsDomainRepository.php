<?php
/**
 * HttpdAliasRepository
 *
 * @author genar
 */
namespace ACS\ACSPanelBundle\Model;

use ACS\ACSPanelUsersBundle\Entity\FosUser;

use ACS\ACSPanelUsersBundle\Doctrine\AclEntityRepository;

class DnsDomainRepository extends AclEntityRepository
{
    public function findByUser(FosUser $user)
    {
        $query = $this->_em->createQuery('SELECT dns FROM ACS\ACSPanelBundle\Entity\DnsDomain dns INNER JOIN dns.domain d WHERE d.user = ?1')->setParameter(1, $user->getId());
        return $query->getResult();
    }

    public function findByUsers(Array $user)
    {
        $query = $this->_em->createQuery('SELECT dns FROM ACS\ACSPanelBundle\Entity\DnsDomain dns INNER JOIN dns.domain d WHERE d.user IN (?1)')->setParameter(1, $user);
        return $query->getResult();
    }

}

?>
