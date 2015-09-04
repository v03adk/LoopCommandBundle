<?php

namespace KzDali\LoopCommandBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StarterCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('loop_command:start')
            ->setDescription('Runs commands from config');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commands = $this->getContainer()->getParameter('loop_command.commands');
        foreach($commands as $command)
        {
            $rC = $this->getApplication()->find($command);
            $returnCode = $rC->run(new ArrayInput(array('command' => $command)), $output);
        }
    }
}