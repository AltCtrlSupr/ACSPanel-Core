<?php

namespace ACS\ACSPanelBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

abstract class CommonTestCase extends WebTestCase
{
    /**
     * Fixtures to load
     */
    private $fixtures = [
        'ACS\ACSPanelBundle\Tests\DataFixtures\LoadUserData',
        'ACS\ACSPanelBundle\Tests\DataFixtures\LoadDomainData',
        'ACS\ACSPanelBundle\Tests\DataFixtures\LoadPlanData',
        'ACS\ACSPanelBundle\Tests\DataFixtures\LoadServiceTypeData',
    ];

    public $client;

    protected $_application;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function setUp()
    {
        $this->loadFixtures($this->fixtures);

        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    protected function requestWithAuth($role, $method, $uri, $parameters = array())
    {
        $this->client = $this->createAuthorizedClient($role);
        return $this->client->request($method, $uri, $parameters, array(), array());
    }

    protected function createAuthorizedClient($username)
    {
        $client = static::makeClient(true);
        $client->followRedirects();
        return $client;
    }

    public function createSuperadminClient()
    {
        $this->client = $this->createAuthorizedClient('superadmin','1234');
        return $this->client;
    }
}
