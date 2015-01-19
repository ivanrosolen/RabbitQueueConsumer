<?php

namespace Xuplau\Queue;

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Consumer
{
    protected $connection;
    protected $channel;
    protected $exchangeResult;
    protected $exchangeKeyResult;
    protected $queue;

    protected $messageBody;

    public function __construct($host, $vhost, $exchangeResult, $exchangeKeyResult, $queue)
    {
        $url = parse_url($host);
        $this->connection = new AMQPConnection($url['host'], $url['port'], $url['user'], $url['pass'], $vhost);

        $this->exchangeResult = $exchangeResult;
        $this->exchangeKeyResult = $exchangeKeyResult;
        $this->queue = $queue;

        $this->setup();
        $this->init();

    }

    protected function setup()
    {
        $this->channel = $this->connection->channel();
        $this->channel->basic_qos(null, 1, null);
        $this->channel->exchange_declare($this->exchangeResult, 'direct', false, true, false);
        $this->channel->queue_declare($this->queue, false, true, false, false);
        $this->channel->queue_bind($this->queue, $this->exchangeResult, $this->exchangeKeyResult);
    }

    public function init()
    {
        $this->channel->basic_consume($this->queue, '', false, false, false, false, array($this, 'readMessage'));
        register_shutdown_function(array($this,'shutdown'));
    }

    public function run()
    {
        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }

    public function shutdown()
    {
        $this->channel->close();
        $this->connection->close();
    }

    public function readMessage($message)
    {
            $this->messageBody = $message->body;

            // Get the message, but now what?
            echo " [x] Received ", $this->messageBody, "\n";

            $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
    }

}