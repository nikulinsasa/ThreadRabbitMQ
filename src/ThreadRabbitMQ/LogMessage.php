<?php
/**
 * Created by PhpStorm.
 * User: sasa
 * Date: 01.12.18
 * Time: 17:31
 */

namespace Sasa\ThreadRabbitMQ;


use Sasa\RabbitMQ\ThreadRabbitMQ;

class LogMessage extends ThreadRabbitMQ
{
    protected function doAction()
    {
        sleep((int)$this->message);
        var_dump('I was wait '.$this->message.'sec');
    }

}