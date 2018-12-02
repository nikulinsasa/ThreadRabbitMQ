<?php
/**
 * Created by PhpStorm.
 * User: sasa
 * Date: 02.12.18
 * Time: 11:32
 */

namespace Sasa\Command;


use Sasa\ThreadRabbitMQ\SleeperThread;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ThreadFunctionCommand extends Command
{

    protected static $defaultName = 'app:threads';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('start');

        $this->threads($output);
        $output->writeln('finish');
    }

    private function threads(OutputInterface $output) {
        $threads = [];
        for($i=5;$i>0;$i--) {
            $threads[$i] = new SleeperThread();
            $threads[$i]->setSleep($i);
            $threads[$i]->start();
            $output->writeln('started output');
        }
    }

}