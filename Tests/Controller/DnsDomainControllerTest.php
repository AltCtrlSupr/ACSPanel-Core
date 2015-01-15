<?php

namespace ACS\ACSPanelBundle\Tests\Controller;

use ACS\ACSPanelBundle\Tests\Controller\CommonTestCase;

class DnsDomainControllerTest extends CommonTestCase
{
    public function testLogItemIndex()
    {
		$client = $this->createSuperadminClient();

		$crawler = $client->request('GET', '/dnsdomain');
		$this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
    }
}

