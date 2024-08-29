<?php
    session_start();
        
    include("connection.php");

    $user_name = $_SESSION['user_name'];
    $teacher_id = $_SESSION['teacher_id'];

    $abbreviation = $_GET['abbreviation'];
    $student_classroom_id = $_GET['student_classroom_id'];

    $query = "SELECT * FROM users WHERE id = $teacher_id";
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['teacher_name'] = $row['user_name'];
        $teacher_name = $_SESSION['teacher_name'];
    }

    $query2 = "SELECT unread_teacher FROM student_classroom WHERE student_classroom_id = '$student_classroom_id'";

    $result2 = mysqli_query($con, $query2);

    while ($row = mysqli_fetch_assoc($result2)) {
        $unread_teacher = $row['unread_teacher'];

        if ($unread_teacher == 1) {
            $query_update = "UPDATE student_classroom SET unread_teacher = 0 WHERE student_classroom_id = '$student_classroom_id'";
            mysqli_query($con, $query_update);
        } 
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consult Teacher</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <?php include("color_theme.php"); ?>
    <style>
        body {
            background-color: #f4f4f4;
            justify-content: center;
            align-items: center; 
            display: flex;
            height: 100vh; 
        }
        #container {
            width: 100%; 
            padding: 40px;
        }
        #chat-box {
            height: 300px;
            border: 1px solid #ccc;
            background-color: #fff;
            overflow-y: scroll;
            padding: 10px;
            font-size: 20px;
            margin-bottom: 20px;
            max-width: 100%;
            word-wrap: break-word;
        }
        #message-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
            font-size: 16px;
            resize: vertical;
        }
        .button {
            margin-top: 10px;
            margin-right: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
    </style>
</head>
<body class="w3-theme-l5">
    <div id="container" class="w3-card-4 w3-theme-l4">
        <h1 style="font-weight: bold;">Consult Teacher</h1>
        <div id="chat-box"></div>
        <div>
            <textarea id="message-input" placeholder="Type your message..." onkeypress="handleKeyPress(event)"></textarea>
            <button onclick="sendMessage()" class="w3-theme-l3 button w3-button w3-padding-large w3-white w3-border">&#x27A4; Send</button>
            <button class="w3-theme-l3 button w3-button w3-padding-large w3-white w3-border" id="startButton">Start Speak</button>
            <button onclick="exitConfirmation()" class="w3-theme-l3 button w3-button w3-padding-large w3-white w3-border">&#x274C; Exit Chat</button><br>
        </div>
    </div>

    <script>
        if (localStorage.getItem('fontSize')) {
            adjustFontSize(localStorage.getItem('fontSize'));
        }
        function adjustFontSize(size) {
            document.body.style.fontSize = size + 'px';
            localStorage.setItem('fontSize', size);
        }
        const startButton = document.getElementById('startButton');
        const output = document.getElementById('message-input');

        const recognition = new webkitSpeechRecognition() || SpeechRecognition();

        recognition.lang = 'en-US';

        recognition.onresult = function(event) {
            const result = event.results[0][0].transcript;
            output.textContent = 'You said: ' + result;
        };

        recognition.onerror = function(event) {
            output.textContent = 'Error occurred in recognition: ' + event.error;
        };

        startButton.addEventListener('click', function() {
            output.textContent = 'Listening...';
            recognition.start();
        });

        function handleKeyPress(event) {
            if (event.shiftKey && event.keyCode === 13) { 
                insertLineBreak(); 
            } else if (event.keyCode === 13) { 
                event.preventDefault(); 
                sendMessage();
            }
        }

        function exitConfirmation(){
            if (confirm("Are you sure you want to exit?")) {
                history.back();
            }
        }

        const username = "<?php echo $user_name ?>";
        const receiver = "<?php echo $teacher_name?>";
        const abbreviation = "<?php echo $abbreviation?>";

        const socket = new WebSocket('ws://localhost:8080/chat');

        socket.onopen = function(event) {
            const message = {
                type: "set_username",
                username: username,
                receiver: receiver
            };
            socket.send(JSON.stringify(message));
            console.log('WebSocket connection opened');
        };

        socket.onmessage = function(event) {
            const message = event.data;
            displayMessage(message);
        };

        function sendMessage() {
            const messageInput = document.getElementById('message-input');
            var messageContent = messageInput.value.trim(); 
            if (messageContent == '') { 
                return;
            }
            const message = {
                type: "send_message",
                username: username,
                sender: username,
                receiver: receiver,
                content: messageInput.value,
                abbreviation: abbreviation
            };
            socket.send(JSON.stringify(message));
            messageInput.value = '';
        }

        function displayMessage(message) {
            const chatBox = document.getElementById('chat-box');
            const messageElement = document.createElement('div');
            messageElement.textContent = message;
            chatBox.appendChild(messageElement);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

    </script>
</body>
</html>