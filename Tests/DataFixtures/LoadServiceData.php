<?php
namespace ACS\ACSPanelBundle\Tests\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use ACS\ACSPanelBundle\Entity\Service;
use ACS\ACSPanelBundle\Entity\FieldType;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadServiceData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // Ftpdservice types
        $ftpdservice = new Service();
        $ftpdservice->setName('Ftpd Testing Service');
        $type = $this->getReference('proftpd');
        $ftpdservice->setType($type);
        $ftpdservice->setUser($this->getReference('user-super-admin'));
        $manager->persist($ftpdservice);

        $webservice = new Service();
        $webservice->setName('web.acs.li');
        $manager->persist($webservice);

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}
