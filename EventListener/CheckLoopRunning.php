<?php

namespace KzDali\LoopCommandBundle\EventListener;

use KzDali\LoopCommandBundle\Controller\LoopCommandInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Kernel;

class CheckLoopRunning
{
    private $kernel;
    private $pidFile;
    private $logPath;
    private $scriptPath;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;

        $kernelRootDir = $this->kernel->getRootDir();
        $this->logPath = $kernelRootDir . '/logs/async_log.log';
        $this->pidFile = $kernelRootDir . '/logs/async.pid';
        $this->scriptPath = $kernelRootDir . '/../vendor/KzDali/Bundle/LoopCommandBundle/Command/_async_exec.php';
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
        * $controller passed can be either a class or a Closure.
        * This is not usual in Symfony but it may happen.
        * If it is a class, it comes in array format
        */
        if (!is_array($controller) || !$controller[0] instanceof LoopCommandInterface) {
            return;
        }

        $isRunning = false;
        if(file_exists($this->pidFile))
        {
            $sPid = file_get_contents($this->pidFile);
            if($sPid !== false)
            {
                $isRunning = $this->checkIsRunning($sPid);
            }
        }

        if(!$isRunning)
        {
            if (substr(php_uname(), 0, 7) == "Windows")
            {
                $this->startWindows();
            }
            else
            {
                $this->startLinux();
            }
        }


    }

    private function checkIsRunning($sPid)
    {
        if (substr(php_uname(), 0, 7) == "Windows")
        {
            $processes = explode( "\n", shell_exec( "tasklist.exe" ));
            $pid = null;
            foreach( $processes as $process )
            {
                if($process == '') continue;
                if( strpos( "Image Name", $process ) === 0 || strpos( "===", $process ) === 0 )
                    continue;
                $matches = false;
                preg_match( "/(.*?)\s+(\d+).*$/", $process, $matches );
                if(count($matches))
                {
                    $pid = $matches[ 2 ];
                    if($pid == $sPid)
                        return true;
                }
            }

            return false;
        }
        else
        {
            exec("ps $sPid", $ProcessState);
            return(count($ProcessState) >= 2);
        }
    }

    private function startWindows()
    {
        $descriptorspec = array (
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
        );
        $pipes = array();

        if ( is_resource( $prog = proc_open("start /b " . 'php '.$this->scriptPath.' > '.$this->logPath, $descriptorspec, $pipes) ) )
        {
            $ppid = proc_get_status($prog);
            $ppid = $ppid['pid'];
        }


        $output = array_filter(explode(" ", shell_exec("wmic process get parentprocessid,processid | find \"$ppid\"")));
        array_pop($output);
        $pid = end($output);

        if(!empty($pid))
        {
            file_put_contents($this->pidFile, $pid);
        }

    }

    private function startLinux()
    {
        $pid = shell_exec('nohup php '.$this->scriptPath.' > '.$this->logPath.' 2>&1 & echo $!');
        if($pid)
        {
            file_put_contents($this->pidFile, trim($pid));
        }

    }
}