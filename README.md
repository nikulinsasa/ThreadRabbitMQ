# Definition

It's study project. I want try write consumer RabbitMQ with pthread.
Maybe I would use phpreact for async send message to RabbitMQ

## About project

Folder rabbitmq includes a class consumer for RabbitMQ (Consumer).
Functions for declare and consume have arguments analogize of amqp library.

Class ThreadRabbitMQ is parent for other threads in the Consumer. The Thread has a string property `message`. 
It's string value of AMQPMessage.

#Docker 

Container for PHP 7.2 (ZTS) with pthreads

For create container

`docker build -t async-rabbit .`

For run examples

`docker run -it --rm --name async-rabbit -v "$PWD":/usr/async-rabbit -w /usr/async-rabbit async-rabbit php run.php`