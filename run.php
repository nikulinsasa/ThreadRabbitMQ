<?php
/**
 * Created by PhpStorm.
 * User: sasa
 * Date: 01.12.18
 * Time: 15:31
 */

include __DIR__.'/vendor/autoload.php';


$app = new \Symfony\Component\Console\Application();
$app->setCommandLoader(new \Sasa\Symfony\CommandLoader(__DIR__.'/src/Command'));
$app->run();

