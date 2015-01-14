<?php
/**
 * ServiceTypeRepository
 *
 * @author genar
 */
namespace ACS\ACSPanelBundle\Model;

use ACS\ACSPanelUsersBundle\Doctrine\AclEntityRepository;

class ServiceRepository extends AclEntityRepository
{
    public function getDbServices()
    {
        $query = $this->_em->createQuery('SELECT s,st FROM ACS\ACSPanelBundle\Entity\Service s INNER JOIN s.type st LEFT JOIN st.parent_type pst WHERE st.name = ?1 OR pst.name = ?1 OR pst.name = ?2')->setParameter(1, 'DB')->setParameter(2, 'Database');
        $result = $query->getResult();
        $ids = array();

        foreach ($result as $key => $st) {
            $ids[] = $st->getId();
        }
        return $ids;

    }

    public function getDNSServices()
    {
        $query = $this->_em->createQuery('SELECT s,st FROM ACS\ACSPanelBundle\Entity\Service s INNER JOIN s.type st LEFT JOIN st.parent_type pst WHERE st.name = ?1 OR pst.name = ?1 OR pst.name = ?2')->setParameter(1, 'DNS')->setParameter(2, 'DNS');
        $result = $query->getResult();
        $ids = array();

        foreach ($result as $key => $st) {
            $ids[] = $st->getId();
        }
        return $ids;

    }

    public function getWebServices()
    {
        $query = $this->_em->createQuery('SELECT s,st FROM ACS\ACSPanelBundle\Entity\Service s INNER JOIN s.type st LEFT JOIN st.parent_type pst WHERE st.name LIKE ?1 OR pst.name LIKE ?1 OR st.name LIKE ?2 OR pst.name LIKE ?2')->setParameter(1, '%Web%')->setParameter(2, '%HTTP%');
        return $query->getResult();
    }

    public function getWebproxyServices()
    {
        $query = $this->_em->createQuery('SELECT s,st FROM ACS\ACSPanelBundle\Entity\Service s INNER JOIN s.type st LEFT JOIN st.parent_type pst WHERE st.name LIKE ?1 OR pst.name LIKE ?1 OR st.name LIKE ?2 OR pst.name LIKE ?2')->setParameter(1, '%Webproxy%')->setParameter(2, '%HTTP proxy%');
        return $query->getResult();

    }



}
