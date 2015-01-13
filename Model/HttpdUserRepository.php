<?php
/**
 * HttpdAliasRepository
 *
 * @author genar
 */
namespace ACS\ACSPanelBundle\Model;

use ACS\ACSPanelUsersBundle\Doctrine\AclEntityRepository;
use ACS\ACSPanelUsersBundle\Entity\FosUser;

class HttpdUserRepository extends AclEntityRepository
{
    public function findByUser(FosUser $user)
    {
        $query = $this->_em->createQuery('SELECT u FROM ACS\ACSPanelBundle\Entity\HttpdUser u INNER JOIN u.httpd_host h INNER JOIN h.domain d WHERE d.user = ?1')->setParameter(1, $user->getId());
        return $query->getResult();
    }
    public function findByUsers(Array $user)
    {
        $query = $this->_em->createQuery('SELECT u FROM ACS\ACSPanelBundle\Entity\HttpdUser u INNER JOIN u.httpd_host h INNER JOIN h.domain d WHERE d.user IN (?1)')->setParameter(1, $user);
        return $query->getResult();
    }
}
