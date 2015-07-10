<?php

namespace ACS\ACSPanelBundle\Tests\Controller;

use ACS\ACSPanelBundle\Tests\Controller\CommonTestCase;

class LogItemControllerTest extends CommonTestCase
{
    public function testLogItemIndex()
    {
        $client = $this->createSuperadminClient();

        $crawler = $client->request('GET', '/logs');
        $this->assertTrue(200 === $this->client->getResponse()->getStatusCode());
    }
}

