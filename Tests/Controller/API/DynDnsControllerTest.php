<?php

namespace ACS\ACSPanelBundle\Tests\Controller\API;

class DynDnsControllerTest extends CommonApiTestCase
{
    public function testUpdateDns()
    {
        $client = $this->createSuperadminClient();

        // DynDNS like call
        // http://username:password@members.dyndns.org/nic/update?hostname=yourhostname&myip=ipaddress
        $crawler = $client->request('GET', '/nic/update?hostname=1domain.tld&myip=8.8.8.8');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        // Check if the respense contents are json
        $this->assertJson($client);
        $this->assertRegExp('/8.8.8.8/', $client->getResponse()->getContent());
    }
}

