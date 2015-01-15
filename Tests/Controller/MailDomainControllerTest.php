<?php

namespace ACS\ACSPanelBundle\Tests\Controller;

use ACS\ACSPanelBundle\Tests\Controller\CommonTestCase;

class MailDomainControllerTest extends CommonTestCase
{
    public function testMailDomainIndex()
    {
		$client = $this->createSuperadminClient();

		$crawler = $client->request('GET', '/maildomain');
		$this->assertTrue(200 === $this->client->getResponse()->getStatusCode());

    }
}
