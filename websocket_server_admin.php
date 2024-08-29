<?php

require __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\App;
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;

class Admin implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";

        $this->chatPartners[$conn->resourceId] = 'admin';
        echo "User ({$conn->resourceId}) is chatting with admin\n";
    }

    protected function getHistoryMessages($username, $receiver) {
        include('connection.php');

        $sql = "SELECT * FROM messages WHERE (sender = '$receiver' AND receiver = '$username') OR (sender = '$username' AND receiver = '$receiver') ORDER BY created_at DESC LIMIT 50";
         
        $result = $con->query($sql);
        
        $historyMessages = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $historyMessages[] = "{$row['sender']}: {$row['content']}";
            }
        }
        
        $con->close();
        
        return $historyMessages;
    }
    

    public function onMessage(ConnectionInterface $from, $msg) {
        $message = json_decode($msg);
        $type = $message->type;
        if ($type === "set_username") {
            $username = $message->username;
            $receiver = $message->receiver;
            $from->username = $username;
            echo "User {$username} connected!\n";
            $this->chatPartners[$from->resourceId] = $receiver;
            $historyMessages = array_reverse($this->getHistoryMessages($username, $receiver));
            foreach ($historyMessages as $message) {
                $from->send($message);
            }
        } else {
            $content = $message->content;
            $sender = $message->sender;
            $receiver = $message->receiver;
            $fullMessage = "{$sender}: {$content}";
            
            $this->broadcastMessage($from, $fullMessage, $receiver, $sender);
        }
    }       

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    public function broadcastMessage(ConnectionInterface $from, $msg, $receiver = null, $sender = null) {
        $message = "{$msg}";
        $unreceive = true;
        $this->saveMessageToDatabase($msg, $receiver, $sender);

        foreach ($this->clients as $client) {
            if ($client->username === $sender) {
                $client->send($message);
            }
            else if (($this->chatPartners[$client->resourceId] === $sender && $client->username === $receiver)){
                $client->send($message);
                $unreceive = false;
            }
        }  
        if($unreceive == true){
            $this->setUnreadDatabase($sender, $receiver);
        }   
    }

    protected function setUnreadDatabase($sender, $receiver) {
        include("connection.php");

        if($receiver == "admin"){
            $updateQuery = "UPDATE users SET admin_notifications = 1 WHERE user_name = '$sender'";
            $updateResult = mysqli_query($con, $updateQuery);
            if (!$updateResult) {
                echo "Error updating unread_forum: " . mysqli_error($con);
            } 
        }else{
            $updateQuery2 = "UPDATE users SET unread_admin = 1 WHERE user_name = '$receiver'";
            $updateResult2 = mysqli_query($con, $updateQuery2);
            if (!$updateResult2) {
                echo "Error updating unread_forum: " . mysqli_error($con);
            } 
        }   
        
        mysqli_close($con);
    }

    protected function saveMessageToDatabase($content, $receiver = null, $sender = null) {
        include("connection.php");

        $pos = strpos($content, ':');

        if ($pos !== false) {
            $content = substr($content, $pos + 2);
        }
        
        $sql = "INSERT INTO messages (sender, content, receiver) VALUES ('$sender', '$content', '$receiver')";

        if ($con->query($sql) === TRUE) {
            echo "New record created successfully\n";
        } else {
            echo "Error: " . $sql . "<br>" . $con->error;
        }

        $con->close();
    }    
}

$adminServer = new App('localhost', 8060);
$adminServer->route('/admin', new WsServer(new Admin())); 
$adminServer->run(); 