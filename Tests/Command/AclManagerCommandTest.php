<?php
namespace ACS\ACSPanelBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use ACS\ACSPanelBundle\Command\AclManagerCommand;


class ListCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new AclManagerCommand());

        $command = $application->find('acl-manager:update-entity');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                'entity' => '\ACS\ACSPanelBundle\Entity\Plan'
            )
        );

        // This should add Plans only for admins
        $this->assertRegExp('/Added/', $commandTester->getDisplay());
    }
}
