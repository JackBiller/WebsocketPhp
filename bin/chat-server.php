<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MyApp\Chat;
// use Ratchet\App;
// use MyApp\BasicPubSub;

require dirname(__DIR__) . '/vendor/autoload.php';

// Your shell script
// $server = new App('localhost');
$server = IoServer::factory(
	new HttpServer(
		new WsServer(
			new Chat()
		)
	),
	8080
);
// $server->route('/pubsub', new BasicPubSub);
$server->run();
