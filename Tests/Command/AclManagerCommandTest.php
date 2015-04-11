<?php

namespace ACS\ACSPanelBundle\Tests\Command;

use ACS\ACSPanelBundle\Command\AclManagerCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class AclManagerCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $kernel = new \AppKernel("test", true);

        $application = new Application($kernel);
        $application->add(new AclManagerCommand());

        $command = $application->find('acl-manager:update-entity');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            'name' => ''
        ));

        $this->assertRegExp('/.../', $commandTester->getDisplay());
    }
}
