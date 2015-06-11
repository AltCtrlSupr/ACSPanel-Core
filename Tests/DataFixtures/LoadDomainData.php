<?php
namespace ACS\ACSPanelBundle\Tests\DataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use ACS\ACSPanelBundle\Entity\Domain;

class LoadDomainData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        // Adding 15 domains for superadmin
        for ($i=0; $i < 15; $i++) {
            $domain1 = new Domain();
            $domain1->setDomain($i . 'domain.tld');
            $domain1->setEnabled(true);
            $domain1->setUser($this->getReference('user-super-admin'));

            $manager->persist($domain1);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}


