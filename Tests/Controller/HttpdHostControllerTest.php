<?php

namespace ACS\ACSPanelBundle\Tests\Controller;

use ACS\ACSPanelBundle\Tests\Controller\CommonTestCase;

class HttpdHostControllerTest extends CommonTestCase
{
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $this->client = $this->createAuthorizedClient('superadmin','1234');

        // Create a new entry in the database
        $crawler = $this->client->request('GET', '/httpdhost/');
		ldd($crawler->html());
        $this->assertTrue(200 === $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->request('GET', '/httpdhost/new');
        $this->assertTrue($crawler->filter('.error-msg:contains(No tienes suficientes recursos para crear HttpdHost)')->count() > 0);
    }
}
