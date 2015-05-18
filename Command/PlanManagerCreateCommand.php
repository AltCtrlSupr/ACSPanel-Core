<?php

namespace ACS\ACSPanelBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PlanManagerCreateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('acs:plan-manager:create')
            ->setDescription('Create plan')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Plan Name'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}
