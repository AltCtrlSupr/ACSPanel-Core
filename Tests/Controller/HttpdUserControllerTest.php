<?php

namespace ACS\ACSPanelBundle\Tests\Controller;

use ACS\ACSPanelBundle\Tests\Controller\CommonTestCase;

class HttpdUserControllerTest extends CommonTestCase
{
    public function testNewHttpdUserEmptyProtectedDir()
    {
	$client = $this->createSuperadminClient();

	// Loading form
        $crawler = $client->request('GET', '/httpduser/new');

        $this->assertTrue($crawler->filter('.error-msg:contains(No tienes suficientes recursos para crear HttpdUser)')->count() > 0);

	// Form should accept empty protected dir
        /*$form = $crawler->selectButton('Create')->form(array(
            'acs_acspanelbundle_httpdusertype[name]' => 'httpd_test',
            'acs_acspanelbundle_httpdusertype[password]' => '1234',
            'acs_acspanelbundle_httpdusertype[protected_dir]' => '',
        ));


        $crawler = $client->submit($form);*/

    }
}
