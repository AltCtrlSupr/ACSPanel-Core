<?php

namespace ACS\ACSPanelBundle\Tests\Controller;

use ACS\ACSPanelBundle\Tests\Controller\CommonTestCase;

class DbControllerTest extends CommonTestCase
{
    public function testDbIndex()
    {
		$client = $this->createSuperadminClient();

		$crawler = $client->request('GET', '/db');
		$this->assertTrue(200 === $this->client->getResponse()->getStatusCode());

    }
}
