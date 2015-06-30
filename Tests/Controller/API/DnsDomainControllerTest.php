<?php

namespace ACS\ACSPanelBundle\Tests\Controller\API;

class DnsDomainControllerTest extends CommonApiTestCase
{
    public function testServiceScenario()
    {
		$client = $this->createSuperadminClient();

		$crawler = $this->client->request('GET', '/api/dnsdomains/index.json');

		$this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Check if the respense contents are json
        $this->assertJson($client);

		$crawler = $this->client->request('GET', '/api/dnsdomains/1/show.json');
		$this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Check if the respense contents are json
        $this->assertJson($client);

        $this->assertRegExp('/{"id":1,"domain":"0domain.tld"/', $client->getResponse()->getContent());

        // Creating new domains with body
        $crawler = $this->client->request('POST', '/api/dnsdomains/create.json', array(
            'acs_acspanelbundle_pdnsdomaintype' => array(
                'domain' => 2,
                'type' => 'MASTER',
                'service' => 1
            )
        ));
        // ldd($this->client->getResponse()->getContent());
		// $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Check if the respense contents are json
        $this->assertJson($client);
    }
}

