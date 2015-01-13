<?php
/**
 * DBRepository
 *
 * @author ZUNbado
 */
namespace ACS\ACSPanelBundle\Model;

use ACS\ACSPanelUsersBundle\Doctrine\AclEntityRepository;

class DBRepository extends AclEntityRepository
{
    public function findByUsers(Array $user)
    {
        $query = $this->_em->createQuery('SELECT d FROM ACS\ACSPanelBundle\Entity\DB d WHERE d.user IN (?1)')->setParameter(1, $user);
        return $query->getResult();
    }
}

?>
