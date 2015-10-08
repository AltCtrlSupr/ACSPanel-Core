<?php
namespace ACS\ACSPanelBundle\Tests\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use ACS\ACSPanelBundle\Entity\Server;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadServerData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {
        // new server
        $server1 = new Server();
        $server1->setName('Testing server');
        $manager->persist($server1);
        $this->addReference('server-1', $server1);

        $manager->flush();
    }
}
