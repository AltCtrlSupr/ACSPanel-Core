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
        'ACS\ACSPanelBundle\Tests\DataFixtures\LoadTestDomainData',
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
        $client = $this->client;
        $container = $client->getContainer();

        $session = $container->get('session');
        /** @var $userManager \FOS\UserBundle\Doctrine\UserManager */
        $userManager = $container->get('fos_user.user_manager');
        /** @var $loginManager \FOS\UserBundle\Security\LoginManager */
        $loginManager = $container->get('fos_user.security.login_manager');
        $firewallName = $container->getParameter('fos_user.firewall_name');

        $user = $userManager->findUserBy(array('username' => $username));

		if(!$user)
			throw new \Exception('No user found');

		$loginManager->loginUser($firewallName, $user);

		// save the login token into the session and put it in a cookie
		$container->get('session')->set('_security_' . $firewallName,
		serialize($container->get('security.context')->getToken()));
		$container->get('session')->save();
		$this->client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $this->client;
    }

    public function createSuperadminClient()
    {
        $this->client = $this->createAuthorizedClient('superadmin','1234');
        return $this->client;
    }
}
