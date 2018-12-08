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

/**
 * consumer of rabbitmq
 * Class Consumer
 * @package Sasa\RabbitMQ
 */
class Consumer
{

    const MAX_POOL = 3;

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

    /**
     * @var \Pool
     */
    private $pool;

    public function __construct($host = self::DEF_HOST,
                                $port = self::DEF_PORT,
                                $user = self::DEF_USER,
                                $password = self::DEF_PASSWORD)
    {
        $this->connection = new AMQPStreamConnection($host, $port, $user, $password);
        $this->channel = $this->connection->channel();

        $this->pool = new \Pool(self::MAX_POOL);
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

        $this->channel->basic_consume($this->queue,
            $consumer_tag,
            $no_local,
            $no_ack,
            $exclusive,
            $nowait,
            function ($data) use ($threadName) {
                $data->delivery_info['channel']->basic_ack($data->delivery_info['delivery_tag']);
                if(!class_exists($threadName)){
                    throw new \Exception('A class '.$threadName.' is absent');
                }
                /** @var ThreadRabbitMQ $thread */
                $thread = new $threadName();
                if(!($thread instanceof ThreadRabbitMQ)) {
                    throw new \Exception('A class must be extended by ThreadRabbitMQ');
                }
                $thread->setMessage($data->body);
                $this->pool->submit($thread);
            },
            $ticket,
            $arguments);

        while (count($this->channel->callbacks)) {
            if ($this->pool->collect() == 0) {
                $this->channel->wait(NULL,TRUE);
            }
        }
    }

    protected function createThread($threadName) {

    }

    public function close()
    {
        $this->channel->close();
        $this->connection->close();
    }


    /**
     * @param int $maxPool
     */
    public function resizePool(int $maxPool): void
    {
        $this->pool->resize($maxPool);
    }

}