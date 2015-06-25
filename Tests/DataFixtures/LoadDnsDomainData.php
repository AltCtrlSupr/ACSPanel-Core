<?php
namespace ACS\ACSPanelBundle\Tests\DataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use ACS\ACSPanelBundle\Entity\DnsDomain;

class LoadDnsDomainData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        // Adding 15 dns-domains for superadmin
        for ($i=0; $i < 15; $i++) {
            $ddomain = new DnsDomain();
            $ddomain->setType('A');
            $ddomain->setEnabled(true);
            $ddomain->setDomain($this->getReference('domain-'. $i));

            $manager->persist($ddomain);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}


