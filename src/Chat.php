<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Msm { 
    public $user;
    public $msm;
    
    function __construct($user, $msm) { 
        $this->user = $user;
        $this->msm = $msm;
    }
}

class Register {
    public $id;
    public $user;

    function __construct($id) { 
        $this->id = $id;
    }
}

class Chat implements MessageComponentInterface {
    protected $clients;
    public $registes = array();

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
        array_push($this->registes, new Register($conn->resourceId));
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $msg = json_decode($msg);
        $op = $msg->op;

        if ($op == 'register') {
            if ($this->register($from->resourceId, $msg->userName)) { 
                $from->send(json_encode(new Msm('Servidor','Cadastro feito com sucesso!')));
            } else { 
                $from->send(json_encode(new Msm('Servidor','Não localizou cadastro!')));
            }
            return;
        }

        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg->msm, $numRecv, $numRecv == 1 ? '' : 's');

        $nome = $this->getRegister($from->resourceId);

        foreach ($this->clients as $client) { 
            if ($from !== $client) { 
                // The sender is not the receiver, send to each client connected
                $client->send(json_encode(new Msm($nome, $msg->msm)));
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        $nome = $this->getRegister($from->resourceId);
        echo "Connection $nome ({$conn->resourceId}) has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    private function register($id, $name) { 
        foreach ($this->registes as $registe) {
            if ($registe->id == $id) {
                $registe->user = $name;
                return true;
            }
        }
        return false;
    }

    private function getRegister($id) { 
        foreach ($this->registes as $registe) {
            if ($registe->id == $id) {
                return $registe->user;
            }
        }
        return 'Servidor';
    }
}