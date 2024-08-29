<?php
    session_start();
        
    include("connection.php");

    $user_name = $_SESSION['user_name'];

    $abbreviation = $_GET['abbreviation'];
    $student_classroom_id = $_GET['student_classroom_id'];

    $query = "SELECT classroom_name FROM classroom_type WHERE abbreviation = '$abbreviation'";
    
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $classroom_name = $row['classroom_name'];
    } else {
        $classroom_name = "Unknown";
    }

    $query2 = "SELECT unread_forum FROM student_classroom WHERE student_classroom_id = '$student_classroom_id'";

    $result2 = mysqli_query($con, $query2);

    while ($row = mysqli_fetch_assoc($result2)) {
        $unread_forum = $row['unread_forum'];
        if ($unread_forum == 1) {
            $query_update = "UPDATE student_classroom SET unread_forum = 0 WHERE student_classroom_id = '$student_classroom_id'";
            mysqli_query($con, $query_update);
        } 
    }
    $query_update2 = "UPDATE student_classroom SET access_time_forum = NOW() WHERE student_classroom_id = '$student_classroom_id'";
    mysqli_query($con, $query_update2);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Zone</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="color_theme.css">
    <?php include("color_theme.php"); ?>
    <style>
        .button {
            margin-top: 10px;
            margin-right: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        #message-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical;
        }
        #social_zone{
            word-wrap: break-word;
        }
    </style>
</head>
<body class="w3-theme-l5">
    <div class="w3-col">
        <div class="w3-row-padding">
            <div class="w3-card w3-round w3-white">
                <div class="w3-theme-l4 w3-container w3-padding">
                    <h1><b>Post Message</b></h1>
                    <textarea class="w3-border w3-padding" id="message-input" placeholder="Type your message..." onkeypress="handleKeyPress(event)"></textarea>
                    <button onclick="sendMessage()" class="w3-theme-l3 button w3-button w3-padding-large w3-white w3-border">&#x27A4; Post</button>
                    <button class="w3-theme-l3 button w3-button w3-padding-large w3-white w3-border" id="startButton">Start Speak</button>
                    <button onclick="exitConfirmation()" class="w3-theme-l3 button w3-button w3-padding-large w3-white w3-border">&#x274C; Exit Chat</button><br>
                </div>
            </div>
        </div>

        <h1 style="margin-left: 50px;"><b>Social Zone --- <?php echo $classroom_name; ?></b></h1>
        <div id="social_zone" class="w3-theme-l4 w3-container w3-card w3-white w3-round w3-margin"></div>
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
        const abbreviation = "<?php echo $abbreviation?>";

        const socket = new WebSocket('ws://localhost:8070/forum');

        socket.onopen = function(event) {
            const message = {
                type: "set_username",
                username: username,
                abbreviation: abbreviation
            };
            socket.send(JSON.stringify(message));
            console.log('WebSocket connection opened');
        };

        socket.onmessage = function(event) {
            const message = JSON.parse(event.data);
            if (message.messageType === "history") {
                displayMessageHistory(message.content);
            } else if (message.messageType === "new") {
                displayMessage(message.content);
            }
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
                content: messageInput.value,
                abbreviation: abbreviation
            };
            socket.send(JSON.stringify(message));
            messageInput.value = '';
        }

        function displayMessageHistory(message) {
            const chatBox = document.getElementById('social_zone');
            const messageParts = message.split(':');
            const senderName = messageParts[0].trim();
            const messageContent = messageParts[1].trim();

            const messageElement = document.createElement('div');
            messageElement.className = 'w3-theme-l3 w3-container w3-card w3-white w3-round w3-margin';
            messageElement.innerHTML = `
                <br>
                <h4>${senderName}</h4>
                <hr class="w3-clear">
                <p>${messageContent}</p>
            `;

            chatBox.appendChild(messageElement);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function displayMessage(message) {
            const chatBox = document.getElementById('social_zone');
            const messageParts = message.split(':');
            const senderName = messageParts[0].trim();
            const messageContent = messageParts[1].trim();

            const messageElement = document.createElement('div');
            messageElement.className = 'w3-theme-l3 w3-container w3-card w3-white w3-round w3-margin';
            messageElement.innerHTML = `
                <br>
                <h4>${senderName}</h4>
                <hr class="w3-clear">
                <p>${messageContent}</p>
            `;

            chatBox.insertBefore(messageElement, chatBox.firstChild);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

    </script>
</body>
</html>