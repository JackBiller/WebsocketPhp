<?php
namespace BasicPubSub;
use Ratchet\Wamp\WampServerInterface;
use Ratchet\ConnectionInterface;

/**
 * When a user publishes to a topic all clients who have subscribed
 * to that topic will receive the message/event from the publisher
 */
class BasicPubSub implements WampServerInterface {
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
        $topic->broadcast($event);
    }

    public function onCall(ConnectionInterface $conn, $id, $topic, array $params) {
        $conn->callError($id, $topic, 'RPC not supported on this demo');
    }

    // No need to anything, since WampServer adds and removes subscribers to Topics automatically
    public function onSubscribe(ConnectionInterface $conn, $topic) {}
    public function onUnSubscribe(ConnectionInterface $conn, $topic) {}

    public function onOpen(ConnectionInterface $conn) {}
    public function onClose(ConnectionInterface $conn) {}
    public function onError(ConnectionInterface $conn, \Exception $e) {}
}