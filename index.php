<?php

require_once __DIR__.'/vendor/autoload.php';

use Xuplau\Queue\Consumer;

define ( 'HOST',                'http://user:pwd@ip:port/' ); // default port 5672
define ( 'VHOST',               '/' );
define ( 'EXCHANGE_RESULT',     'xxx' );
define ( 'EXCHANGE_KEY_RESULT', 'xxx' );
define ( 'QUEUE',               'xxx' );


$worker = new Consumer( HOST, VHOST, EXCHANGE_RESULT, EXCHANGE_KEY_RESULT, QUEUE );
$worker->run();