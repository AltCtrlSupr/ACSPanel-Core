<?php
namespace ACS\ACSPanelBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use ACS\ACSPanelBundle\Command\PlanManagerCreateCommand;


class PlanManagerCreateCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new PlanManagerCreateCommand());

        $command = $application->find('acs:plan-manager:create');

        // Tests for Plan entity
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
            )
        );
    }
}

