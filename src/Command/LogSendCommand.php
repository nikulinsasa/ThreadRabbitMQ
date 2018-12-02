<?php
/**
 * Created by PhpStorm.
 * User: sasa
 * Date: 02.12.18
 * Time: 12:49
 */

namespace Sasa\Command;


use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Sasa\RabbitMQ\Consumer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LogSendCommand extends Command
{
    protected static $defaultName = 'app:log-send';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = new AMQPStreamConnection('192.168.44.103',Consumer::DEF_PORT,'user','abc123');
        $channel = $connection->channel();
        $channel->queue_declare('test1', false, true);
        $time = microtime(true);
        $channel->basic_publish($this->createMessage(10), '', 'test1');
        $output->writeln(microtime(true)-$time);
        $channel->basic_publish($this->createMessage(1), '', 'test1');
        $channel->basic_publish($this->createMessage(5), '', 'test1');
        $channel->basic_publish($this->createMessage(8), '', 'test1');
        $channel->basic_publish($this->createMessage(60), '', 'test1');
        $channel->basic_publish($this->createMessage(60), '', 'test1');
        $channel->basic_publish($this->createMessage(60), '', 'test1');
    }

    private function createMessage($seconds):AMQPMessage {
        return new AMQPMessage($seconds);
    }
}