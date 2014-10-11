<?php

namespace ACS\ACSPanelBundle\Tests\Controller;

use ACS\ACSPanelBundle\Tests\Controller\CommonTestCase;

class HttpdHostControllerTest extends CommonTestCase
{
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $this->client = $this->createAuthorizedClient('superadmin','1234');

        // Create a new entry in the database
        $crawler = $this->client->request('GET', '/httpdhost/');
        $this->assertTrue(200 === $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->request('GET', '/httpdhost/new');
        $this->assertTrue($crawler->filter('.error-msg:contains(No tienes suficientes recursos para crear HttpdHost)')->count() > 0);
        /*
        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'httpdhost[field_name]'  => 'Test',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertTrue($crawler->filter('td:contains("Test")')->count() > 0);

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Edit')->form(array(
            'httpdhost[field_name]'  => 'Foo',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertTrue($crawler->filter('[value="Foo"]')->count() > 0);

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
         */
    }
}
