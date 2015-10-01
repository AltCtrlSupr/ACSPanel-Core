<?php
namespace ACS\ACSPanelBundle\Tests\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use ACS\ACSPanelBundle\Entity\Service;
use ACS\ACSPanelBundle\Entity\FieldType;

class LoadServiceData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // Ftpdservice types
        $ftpdservice = new Service();
        $ftpdservice->setName('Ftpd Testing Service');

        $manager->persist($ftpdservice);
        $manager->flush();
    }
}

