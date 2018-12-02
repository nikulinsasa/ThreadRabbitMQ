<?php
/**
 * Created by PhpStorm.
 * User: sasa
 * Date: 01.12.18
 * Time: 15:34
 */

namespace Sasa\Command;


use Sasa\ThreadRabbitMQ\LogMessage;
use Sasa\RabbitMQ\Consumer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LogMessageCommand extends Command
{

    protected static $defaultName = 'app:log-message';

    protected function configure()
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $consumer = new Consumer('192.168.44.103',Consumer::DEF_PORT,'user','abc123');
        $consumer->declareQueue('test1',false,true);
        $consumer->consume(LogMessage::class,'',false,true);
    }

}