<?php

namespace ACS\ACSPanelBundle\Tests\Controller;

use ACS\ACSPanelBundle\Tests\Controller\CommonTestCase;

class DomainControllerTest extends CommonTestCase
{
    public function testDomainScenario()
    {
        $client = $this->createSuperadminClient();

        $crawler = $this->client->request('GET', '/domain');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->request('GET', '/domain/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Form should accept empty protected dir
        $form = $crawler->selectButton('Create')->form(array(
            'acs_acspanelbundle_domaintype[domain]' => 'test.com',
            'acs_acspanelbundle_domaintype[add_dns_domain]' => true,
        ));

        $crawler = $client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}

