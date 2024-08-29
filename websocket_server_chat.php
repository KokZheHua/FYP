<?php

require __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\App;
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;

class Chat implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";

        $this->chatPartners[$conn->resourceId] = 'teacher';
        echo "User ({$conn->resourceId}) is chatting with teacher\n";
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
            $abbreviation = $message->abbreviation; 
            $fullMessage = "{$sender}: {$content}";
            
            $this->broadcastMessage($from, $fullMessage, $receiver, $sender, $abbreviation);
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

    public function broadcastMessage(ConnectionInterface $from, $msg, $receiver = null, $sender = null, $abbreviation) {
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
            $this->setUnreadDatabase($sender, $receiver, $abbreviation);
        }   
    }

    protected function setUnreadDatabase($sender, $receiver, $abbreviation) {
        include("connection.php");
    
        $query = "SELECT * FROM users WHERE user_name = '$receiver'";
        $result = mysqli_query($con, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $userRole = $row['role'];
            $userId = $row['id'];

            if($userRole == 'teacher'){
                $query2 = "SELECT * FROM users WHERE user_name = '$sender'";
                $result2 = mysqli_query($con, $query2);

                if ($result2 && mysqli_num_rows($result2) > 0) {
                    $row2 = mysqli_fetch_assoc($result2);
                    $userId_student = $row2['id'];
                    $updateQuery = "UPDATE student_classroom SET teacher_notifications = 1 WHERE id = $userId_student AND abbreviation = '$abbreviation'";
                    $updateResult = mysqli_query($con, $updateQuery);
                }
                if (!$updateResult) {
                    echo "Error updating unread_forum: " . mysqli_error($con);
                }
            }else{
                $updateQuery = "UPDATE student_classroom SET unread_teacher = 1 WHERE id = $userId AND abbreviation = '$abbreviation'";
                $updateResult = mysqli_query($con, $updateQuery);
                if (!$updateResult) {
                    echo "Error updating unread_forum: " . mysqli_error($con);
                }
            }
        } else {
            echo "User not found in the database.";
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

$chatServer = new App('localhost', 8080);
$chatServer->route('/chat', new WsServer(new Chat())); 
$chatServer->run(); 