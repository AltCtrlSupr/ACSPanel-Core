<?php
/**
 * HttpdAliasRepository
 *
 * @author genar
 */
namespace ACS\ACSPanelBundle\Model;

use ACS\ACSPanelUsersBundle\Entity\FosUser;

use ACS\ACSPanelUsersBundle\Doctrine\AclEntityRepository;

class MailDomainRepository extends AclEntityRepository
{
    public function findByUser(FosUser $user)
    {
        $query = $this->_em->createQuery('SELECT a FROM ACS\ACSPanelBundle\Entity\MailDomain a WHERE a.user = ?1')->setParameter(1, $user->getId());
        return $query->getResult();
    }

    public function findByUsers(Array $ids)
    {
        $query = $this->_em->createQuery('SELECT a FROM ACS\ACSPanelBundle\Entity\MailDomain a WHERE a.user IN (?1)')->setParameter(1, $ids);
        return $query->getResult();
    }

}

?>
