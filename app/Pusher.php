<?php 
namespace App;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;


class Pusher implements MessageComponentInterface
{
    /**
     * A lookup of all the topics clients have subscribed to
     */
    protected $clients;

    protected $cid = array();

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
        $this->cid[$conn->WebSocket->request->getQuery()->toArray()["aid"]] = $conn;

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients);
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        foreach ($this->clients as $client) {
            var_dump("aabbbccc ".$client->WebSocket->request->getQuery()->toArray());
            if ($from !== $client  ) {
                //The sender is not the receiver, send to each client connected
                $client->send($msg);
            }
        }
    }

    public function onSend($msg) {
        $numRecv = count($this->clients);
        echo sprintf('Server sending message "' . $msg . '" to all other connection') . "\n";

        foreach ($this->clients as $client) {
            var_dump($this->cid);
            //if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send($msg);
            //}
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
    
}