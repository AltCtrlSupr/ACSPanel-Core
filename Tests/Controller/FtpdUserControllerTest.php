<?php

namespace ACS\ACSPanelBundle\Tests\Controller;

use ACS\ACSPanelBundle\Tests\Controller\CommonTestCase;

class FtpdUserControllerTest extends CommonTestCase
{
    public function testFtpdUserIndex()
    {
        $client = $this->createSuperadminClient();

        $crawler = $client->request('GET', '/ftpduser');
        $this->assertTrue(200 === $this->client->getResponse()->getStatusCode());

    }
}
