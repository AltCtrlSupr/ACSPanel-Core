<?php

namespace ACS\ACSPanelBundle\Tests\Controller\API;

use ACS\ACSPanelBundle\Tests\Controller\CommonTestCase;

class DomainControllerTest extends CommonTestCase
{
    public function testDomainScenario()
    {
		$client = $this->createSuperadminClient();

		$crawler = $this->client->request('GET', '/api/domains/index.json');

		$this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Check if the respense contents are json
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $this->assertRegExp('/{"id":1,"domain":"0domain.tld"/', $client->getResponse()->getContent());

        $this->assertNotRegExp('/password/', $client->getResponse()->getContent());

        // Show one domain
		$crawler = $this->client->request('GET', '/api/domains/1/show.json');

		$this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Check if the respense contents are json
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

    }
}

