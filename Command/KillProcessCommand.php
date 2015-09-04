<?php

namespace KzDali\LoopCommandBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class KillProcessCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('loop_command:kill_process')
            ->setDescription('Kills loop command process');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $this->getContainer()->get('kernel')->getRootDir().'/logs/async.pid';
        if(file_exists($path))
        {
            $sPid = file_get_contents($path);
            if($sPid !== false)
            {
                if (substr(php_uname(), 0, 7) == "Windows")
                {
                    $output->writeln(passthru("taskkill /F /PID $sPid"));
                }
                else
                {
                    $output->writeln(passthru('kill -9 '.$sPid));
                }
            }
        }
    }
}