<?php
/**
 * Created by PhpStorm.
 * User: sasa
 * Date: 01.12.18
 * Time: 16:59
 */

namespace Sasa\RabbitMQ;


/**
 * Поток выполнения запроса
 * @package Sasa\RabbitMQ
 */
abstract class ThreadRabbitMQ extends \Thread
{

    /**
     * @var string
     */
    protected $message;

    private $isFinished = FALSE;

    abstract protected function doAction();

    public function run()
    {
        $this->doAction();
        $this->isFinished = TRUE;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function isFinished(): bool
    {
        return $this->isFinished;
    }

}