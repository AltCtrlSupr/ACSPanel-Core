<?php

namespace ACS\ACSPanelBundle\Tests\Controller;

use ACS\ACSPanelBundle\Tests\Controller\CommonTestCase;

class DnsDomainControllerTest extends CommonTestCase
{
    public function testDnsDomainScenario()
    {
		$client = $this->createSuperadminClient();

		$crawler = $this->client->request('GET', '/dnsdomain');
		$this->assertEquals(200, $this->client->getResponse()->getStatusCode());

		$crawler = $this->client->request('GET', '/dnsdomain/new');
		$this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}

