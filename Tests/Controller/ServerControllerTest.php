<?php

namespace ACS\ACSPanelBundle\Tests\Controller;

use ACS\ACSPanelBundle\Tests\Controller\CommonTestCase;

class serverControllerTest extends CommonTestCase
{
    public function testServerIndex()
    {
        $client = $this->createSuperadminClient();

        // Loading form
        $crawler = $client->request('GET', '/server');
        $this->assertTrue(200 === $this->client->getResponse()->getStatusCode());

    }
}
