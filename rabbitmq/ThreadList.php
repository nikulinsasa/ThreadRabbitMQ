<?php
/**
 * Created by PhpStorm.
 * User: sasa
 * Date: 02.12.18
 * Time: 13:24
 */

namespace Sasa\RabbitMQ;

/**
 * Список активных потоков
 * Class ThreadList
 * @package Sasa\RabbitMQ
 */
class ThreadList
{

    const MAX_THREADS = 2;

    private $list = [];


    public function append(ThreadRabbitMQ $thread)
    {
        $this->list[] = $thread;
    }

    public function removeInactive()
    {
        /** @var ThreadRabbitMQ $item */
        foreach ($this->list as $index => $item) {
            if ($item->isFinished()) {
                unset($this->list[$index]);
            }
        }
    }

    public function isNotFullList(): bool
    {
        return count($this->list) < self::MAX_THREADS;
    }

}