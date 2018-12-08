<?php
/**
 * Created by PhpStorm.
 * User: sasa
 * Date: 01.12.18
 * Time: 15:34
 */

namespace Sasa\Command;


use Sasa\RabbitMQ\Consumer;
use Sasa\ThreadRabbitMQ\SleepMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SleepMessageCommand extends Command
{

    protected static $defaultName = 'app:sleep-message';

    protected function configure()
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $consumer = new Consumer('192.168.44.103', Consumer::DEF_PORT, 'user', 'abc123');
        $consumer->declareQueue('test1', FALSE, FALSE, FALSE, FALSE);
        $consumer->consume(SleepMessage::class, '', FALSE, FALSE);
    }

}