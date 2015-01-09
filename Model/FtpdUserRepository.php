<?php
/**
 * HttpdAliasRepository
 *
 * @author genar
 */
namespace ACS\ACSPanelBundle\Model;

use Doctrine\ORM\EntityRepository;

class FtpdUserRepository extends EntityRepository
{
    public function findByUsers(Array $user)
    {
        $query = $this->_em->createQuery('SELECT f FROM ACS\ACSPanelBundle\Entity\FtpdUser f WHERE f.user IN (?1)')->setParameter(1, $user);
        return $query->getResult();
    }
}
