<?php

require __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\App;
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;

class Forum implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
        $this->chatPartners[$conn->resourceId] = 'Unknown';
    }

    protected function getHistoryMessages($abbreviation) {
        include('connection.php');

        $sql = "SELECT * FROM forum_data WHERE abbreviation = '$abbreviation' ORDER BY created_at DESC LIMIT 1000";
         
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
            $abbreviation = $message->abbreviation;
            $from->username = $username;
            echo "User {$username} connected!\n";
            $this->chatPartners[$from->resourceId] = $abbreviation;
            $historyMessages = $this->getHistoryMessages($abbreviation);
            foreach ($historyMessages as $message) {
                $historyMessage = [
                    'messageType' => 'history',
                    'content' => $message,
                ];
                $from->send(json_encode($historyMessage));
            }
        } else {
            $content = $message->content;
            $sender = $message->sender;
            $abbreviation = $message->abbreviation;
            $fullMessage = "{$sender}: {$content}";
            
            $this->broadcastMessage($from, $fullMessage, $sender, $abbreviation);
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

    public function broadcastMessage(ConnectionInterface $from, $msg, $sender = null, $abbreviation) {
        $message = "{$msg}";
        $received = [];
        $this->saveMessageToDatabase($msg, $sender, $abbreviation);

        foreach ($this->clients as $client) {
            if ($this->chatPartners[$client->resourceId] === $abbreviation) {
                $newMessage = [
                    'messageType' => 'new',
                    'content' => $message
                ];
                $client->send(json_encode($newMessage));
                $received[] = $client->username;
            }
        }
        $this->setUnreadDatabase($received, $abbreviation);    
    }

    protected function setUnreadDatabase($received, $abbreviation) {
        include("connection.php");
    
        $idList = [];

        foreach ($received as $client) {
            $query = "SELECT id FROM users WHERE user_name = '$client'";
            $result = mysqli_query($con, $query);
            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $idList[] = $row['id'];
            }
        }    

        $updateQuery = "UPDATE student_classroom SET unread_forum = 1 WHERE abbreviation = '$abbreviation' AND id NOT IN (" . implode(",", $idList) . ")";
        $updateResult = mysqli_query($con, $updateQuery);
        
        mysqli_close($con);
    }
    

    protected function saveMessageToDatabase($content, $sender = null, $abbreviation) {
        include("connection.php");

        $pos = strpos($content, ':');

        if ($pos !== false) {
            $content = substr($content, $pos + 2);
        }
        
        $sql = "INSERT INTO forum_data (sender, content, abbreviation) VALUES ('$sender', '$content', '$abbreviation')";

        if ($con->query($sql) === TRUE) {
            echo "New record created successfully\n";
        } else {
            echo "Error: " . $sql . "<br>" . $con->error;
        }

        $con->close();
    }    

}

$forumServer = new App('localhost', 8070);
$forumServer->route('/forum', new WsServer(new Forum()));
$forumServer->run();