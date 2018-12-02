<?php
/**
 * Created by PhpStorm.
 * User: sasa
 * Date: 01.12.18
 * Time: 16:15
 */

namespace Sasa\RabbitMQ;


use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


class Consumer
{

    const DEF_USER = 'guest';
    const DEF_PASSWORD = 'guest';
    const DEF_HOST = 'localhost';
    const DEF_PORT = 5672;

    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    /**
     * @var AMQPChannel
     */
    private $channel;

    private $queue;


    public function __construct($host = self::DEF_HOST,
                                $port = self::DEF_PORT,
                                $user = self::DEF_USER,
                                $password = self::DEF_PASSWORD)
    {
        $this->connection = new AMQPStreamConnection($host, $port, $user, $password);
        $this->channel = $this->connection->channel();
        $this->channel->basic_qos(NULL, 2, NULL);
    }

    /**
     * Создаем очередь
     * @param string $queue - название очереди
     * @param bool $passive
     * @param bool $durable - очередь останеца после перезагрузки
     * @param bool $exclusive - очередь будет доступна только 1 каналу
     * @param bool $auto_delete - очередь удалится после отключения от канала
     * @param bool $nowait - без ожидания
     * @param array $arguments
     * @param null $ticket
     */
    public function declareQueue($queue = '',
                                 $passive = FALSE,
                                 $durable = FALSE,
                                 $exclusive = FALSE,
                                 $auto_delete = TRUE,
                                 $nowait = FALSE,
                                 $arguments = [],
                                 $ticket = NULL)
    {

        $this->queue = $queue;

        $this->channel->queue_declare(
            $this->queue,
            $passive,
            $durable,
            $exclusive,
            $auto_delete,
            $nowait,
            $arguments,
            $ticket
        );
    }

    /**
     * @param $exchange
     * @param bool $if_unused
     * @param bool $nowait
     * @param null $ticket
     */
    public function declareExchange($exchange,
                                    $if_unused = FALSE,
                                    $nowait = FALSE,
                                    $ticket = NULL)
    {
        $this->channel->exchange_delete($exchange, $if_unused, $nowait, $ticket);
    }

    /**
     * @param string $threadName
     * @param string $consumer_tag идентификатор прослушивателя
     * @param bool $no_local - не получать отправленные сообщения
     * @param bool $no_ack - сообщать серверу, если сообщение отклонено
     * @param bool $exclusive - только 1 на очередь
     * @param bool $nowait - не ждать
     * @param null $ticket
     * @param array $arguments
     * @throws \Exception
     */
    public function consume(
        string $threadName,
        $consumer_tag = '',
        $no_local = FALSE,
        $no_ack = FALSE,
        $exclusive = FALSE,
        $nowait = FALSE,
        $ticket = NULL,
        $arguments = [])
    {

        if ($this->channel == NULL) {
            throw new \Exception('queue not connected');
        }

        $threadsList = new ThreadList();

        $this->channel->basic_consume($this->queue,
            $consumer_tag,
            $no_local,
            $no_ack,
            $exclusive,
            $nowait,
            function ($data) use ($threadName, $threadsList) {
                var_dump($threadsList->isNotFullList(),$data->body);
                if ($threadsList->isNotFullList()) {
                    /** @var ThreadRabbitMQ $thread */
                    $thread = new $threadName();
                    $thread->setMessage($data->body);
                    $thread->start();
                    $threadsList->append($thread);
                } else {
                    var_dump($data->body,$this->channel);
                    $this->channel->basic_publish(new AMQPMessage($data->body), '', $this->queue);
                }
            }, $ticket, $arguments);

        while (count($this->channel->callbacks)) {
            $threadsList->removeInactive();
            if ($threadsList->isNotFullList()) {
                $this->channel->wait(NULL, TRUE);
            }
        }
    }

    public function close()
    {
        $this->channel->close();
        $this->connection->close();
    }

}