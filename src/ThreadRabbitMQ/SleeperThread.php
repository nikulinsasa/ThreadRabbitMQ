<?php
/**
 * Created by PhpStorm.
 * User: sasa
 * Date: 02.12.18
 * Time: 11:42
 */

namespace Sasa\ThreadRabbitMQ;


class SleeperThread extends \Thread
{

    private $sleep;

    public function run() {
        sleep($this->sleep);
        echo 'I had slept at '.$this->sleep.'sec';
    }

    /**
     * @return mixed
     */
    public function getSleep()
    {
        return $this->sleep;
    }

    /**
     * @param mixed $sleep
     */
    public function setSleep($sleep)
    {
        $this->sleep = $sleep;
    }

}