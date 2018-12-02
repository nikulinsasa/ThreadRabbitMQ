## Definition

It's study project. I want try write consumer RabbitMQ with pthread.
Maybe I would use phpreact for async send message to RabbitMQ

If you would use PThread you need use some global array of threads.


docker build -t async-rabbit .

docker run -it --rm --name async-rabbit -v "$PWD":/usr/async-rabbit -w /usr/async-rabbit async-rabbit php main.php