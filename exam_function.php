<?php 
    
    $exam_id = $_SESSION['exam_id'];
    $student_classroom_id = $_SESSION['student_classroom_id'];

    include("connection.php");
    
    if (!$con) {
        die(mysqli_connect_error());
    }
    
    $question_title = [];
    $options = [];
    $answer = [];
    $mark = [];
    $optionA = [];
    $optionB = [];
    $optionC = [];
    $optionD = [];
    $optionLong = [];
    $time_limit_min = null;
    $optionsArray = "";

    $query = "SELECT question_id FROM exam_question WHERE exam_id = '$exam_id' AND student_classroom_id = '$student_classroom_id'";
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) > 0) {
        $question_id_list = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $question_id_list[] = $row['question_id'];
        }
        $totalItems = count($question_id_list);
        
        foreach ($question_id_list as $question_id){
            $query = "SELECT question_title, options, answer, mark FROM question_data WHERE question_id = '$question_id'";
            $result = mysqli_query($con, $query);
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $question_title[] = $row['question_title'];
                $options[] = $row['options'];
                $answer[] = $row['answer'];
                $mark[] = $row['mark'];
                if($row['options'] == null){
                    $optionLong[] = "longAnswer";
                    $optionA[] = isset($optionsArray[0]) ? $optionsArray[0] : '';
                    $optionB[] = isset($optionsArray[1]) ? $optionsArray[1] : '';
                    $optionC[] = isset($optionsArray[2]) ? $optionsArray[2] : '';
                    $optionD[] = isset($optionsArray[3]) ? $optionsArray[3] : '';
                }else{
                    $optionLong[] = null;
                    $optionsArray = explode('/~/', $row['options']);
                    $optionA[] = isset($optionsArray[0]) ? $optionsArray[0] : '';
                    $optionB[] = isset($optionsArray[1]) ? $optionsArray[1] : '';
                    $optionC[] = isset($optionsArray[2]) ? $optionsArray[2] : '';
                    $optionD[] = isset($optionsArray[3]) ? $optionsArray[3] : '';
                }
            }
        }
    }
    $query = "SELECT time_limit_min FROM exam_data WHERE exam_id = '$exam_id'";
    $result = mysqli_query($con, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $time_limit_min = $row['time_limit_min'];
    }
    mysqli_close($con);
?>

<script>
    const examData = [];
    const optionLongList = [];
    <?php for ($i = 0; $i < $totalItems; $i++) : ?>
        <?php if ($optionLong[$i] !== null) : ?>
        <?php  $longAnswer = $optionLong[$i];?>
            examData.push({
                question_id: '<?php echo $question_id_list[$i] ?>',
                question: '<?php echo $question_title[$i] ?>',
                options: '<?php echo $longAnswer ?>',
                answer: '<?php echo $answer[$i] ?>',
                mark: '<?php echo $mark[$i] ?>'
            });
        <?php else : ?>
            examData.push({
                question_id: '<?php echo $question_id_list[$i] ?>',
                question: '<?php echo $question_title[$i] ?>',
                options: ['<?php echo $optionA[$i] ?>', '<?php echo $optionB[$i] ?>', '<?php echo $optionC[$i] ?>', '<?php echo $optionD[$i] ?>'],
                answer: '<?php echo $answer[$i] ?>',
                mark: '<?php echo $mark[$i] ?>'
            });
        <?php endif;?>
        optionLongList.push({
            longQuestion: '<?php echo $optionLong[$i] ?>'
        });
    <?php endfor; ?>

    function open_side_bar() {
        document.getElementById("mySidebar").style.display = "block";
        document.getElementById("side_bar").style.display = "none";
        document.getElementById("exit_btn").style.display = "none";
        document.querySelector(".w3-main").style.marginLeft = "300px"; 
    }

    function close_side_bar() {
        document.getElementById("mySidebar").style.display = "none";
        document.getElementById("side_bar").style.display = "inline-block";
        document.getElementById("exit_btn").style.display = "inline-block";
        document.querySelector(".w3-main").style.marginLeft = "0px"; 
    }

    function toggle_side_bar() {
        var sideBar = document.getElementById("mySidebar");
        if (sideBar.style.display == "none") {
            open_side_bar();
        } 
        else if (sideBar.style.display == "block") {
            close_side_bar();
        }
    }

    const startButton = document.getElementById('startButton');

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
        const output = document.getElementById('output');
        output.textContent = 'Listening...';
        recognition.start();
    });

    const sideBarContainer = document.getElementById('question_sideBar');
    const examContainer = document.getElementById('exam');
    const submitButton = document.getElementById('submit');
    const flagButton = document.getElementById('flag');
    const exitButton = document.getElementById('exit');
    var readButton = document.getElementById("read");

    let currentQuestion = 0;
    let incorrectAnswers = new Array(examData.length);
    let selectedOption_list = new Array(examData.length);
    let longAnswerToSave = new Array(examData.length);

    function setFlag(id) {
        document.querySelectorAll('.side_bar_item').forEach(item => {
            if (item.id == id) {
                if (!item.classList.contains('flag_css')) {
                    item.classList.add('flag_css');
                }
                else {
                    item.classList.remove('flag_css');
                }
            }
        });
    }

    function setActive(id) {
        event.preventDefault();
        document.querySelectorAll('.side_bar_item').forEach(item => {
            if (item.id == id) {
                item.classList.add('w3-black');
                item.classList.add('w3-hover-black');
                currentQuestion = parseInt(id);
                if (currentQuestion == examData.length) {
                    checkAllDone();
                }else{
                    displayQuestion();
                }
            } else {
                item.classList.remove('w3-black');
                item.classList.remove('w3-hover-black');
            }
        });
    }

    function questionDone(id) {
        var hasDone = false;
        var longAnswerInput = document.querySelector('.long-answer-input');
        if (longAnswerInput && longAnswerInput.value.trim() !== '') {
            hasDone = true;
        }else{
            document.querySelectorAll('.option').forEach(btn => {
                if (btn.classList.contains('selected')) {
                    hasDone = true;
                    return false;
                }
            });
        }    
        if (hasDone) {
            document.querySelectorAll('.side_bar_question').forEach(item => {
                if (item.id == id) {
                    item.classList.add('w3-text-teal');
                }
            });
        }else{
            document.querySelectorAll('.side_bar_question').forEach(item => {
                if (item.id == id) {
                    item.classList.remove('w3-text-teal');
                }
            });
        }
    }

    function displaySideBarItem() {
        for (var i = 0; i <= examData.length; i++) {
            if(i == examData.length){
                var link = document.createElement('a');
                link.href = '#';
                link.onclick = function() { 
                    setActive(this.id); 
                };
                link.id = i;
                link.classList.add('side_bar_item', 'w3-bar-item', 'w3-button', 'w3-padding');
                link.textContent = "Final Submit";
            }else{
                var link = document.createElement('a');
                link.href = '#';
                link.onclick = function() { 
                    setActive(this.id); 
                };
                link.id = i;
                if (i === 0) {
                    link.classList.add('w3-black'); 
                    link.classList.add('w3-hover-black'); 
                }
                link.classList.add('side_bar_item', 'side_bar_question', 'w3-bar-item', 'w3-button', 'w3-padding');
                link.textContent = "Question" + (i + 1);
            }

            sideBarContainer.appendChild(link);
        }
    }

    function shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
    }

    function speak_question(currentQuestion, question, mark) {
        const speech = new SpeechSynthesisUtterance('Question' + currentQuestion + ' ' + 'Question Description' + question + ' ' + 'Mark' + mark);
        speech.lang = 'en-US';
        window.speechSynthesis.speak(speech);
    }

    function speakText(text) {
        const speech = new SpeechSynthesisUtterance(text);
        speech.lang = 'en-US';
        window.speechSynthesis.speak(speech);
    }

    function displayQuestion() {
        flagButton.style.display = 'inline-block';
        readButton.style.display = 'inline-block';
        submitButton.style.display = 'inline-block';
        const questionData = examData[currentQuestion];
        const questionElement = document.createElement('div');
        questionElement.className = 'question';
        questionElement.innerHTML = (currentQuestion + 1) + ". " + questionData.question + " (" + questionData.mark + "marks)";

        const optionsElement = document.createElement('div');
        optionsElement.className = 'options';

        if (!Array.isArray(questionData.options)) {
            const longAnswerInput = document.createElement('textarea');
            longAnswerInput.placeholder = 'Enter your answer here...';
            longAnswerInput.name = 'long_answer';
            longAnswerInput.id = 'output';
            longAnswerInput.className = 'long-answer-input';
            startButton.style.display = 'inline-block';
            optionsElement.appendChild(longAnswerInput);
        }else{
            startButton.style.display = "none";
            const shuffledOptions = [...questionData.options];
            shuffleArray(shuffledOptions);

            for (let i = 0; i < shuffledOptions.length; i++) {
                const button = document.createElement('button');
                button.textContent = shuffledOptions[i];
                button.classList.add('option', 'w3-button', 'w3-padding-large', 'w3-white', 'w3-border');

                button.addEventListener('click', () => {
                    const allButtons = document.querySelectorAll('.option');
                    allButtons.forEach(btn => {
                        if (btn !== button) {
                            btn.classList.remove('selected');
                            btn.classList.remove('w3-black');
                            btn.classList.remove('w3-hover-black'); 
                        }
                    });
                    button.classList.add('selected');
                    button.classList.add('w3-black');
                    button.classList.add('w3-hover-black'); 
                });

                optionsElement.appendChild(button);
            }
        }
        readButton.onclick = function() {
            speak_question(currentQuestion+1, questionData.question, questionData.mark, );
            const options = document.querySelectorAll('.option');

            options.forEach((option, index) => {
                const optionText = option.textContent;

                setTimeout(() => {
                    speakText(optionText);
                }, index * 1000); 
            });

        };

        examContainer.innerHTML = '';
        examContainer.appendChild(questionElement);
        examContainer.appendChild(optionsElement);
        
        getPreviousAnswer();
    }
    
    function getPreviousAnswer(){
        if(longAnswerToSave[currentQuestion] !== undefined){
            document.querySelector('.long-answer-input').value = longAnswerToSave[currentQuestion];
        }
        else if (selectedOption_list[currentQuestion] !== undefined) {
            document.querySelectorAll('.option').forEach(btn => {
                if(btn.textContent == selectedOption_list[currentQuestion]){
                    btn.classList.add('selected'); 
                    btn.classList.add('w3-black');
                    btn.classList.add('w3-hover-black'); 
                }
            });
        }
    }

    function checkAllDone() {
        startButton.style.display = "none";
        const questionElement = document.createElement('div');
        questionElement.className = 'final_submit';
        
        var allDone = true;
        document.querySelectorAll('.side_bar_question').forEach(item => {
            if (!item.classList.contains('w3-text-teal')) {
                allDone = false;
                return false;
            }
        });
        
        if (!allDone) {
            questionElement.innerHTML = 'Question not done yet!';
            flagButton.style.display = 'none';
            readButton.style.display = 'none';
            submitButton.style.display = 'none';
        } else {
            questionElement.innerHTML = 'Done all question!';
            flagButton.style.display = 'none';
            readButton.style.display = 'none';
        }

        examContainer.innerHTML = '';
        examContainer.appendChild(questionElement);
    }

    function checkAnswer() {
        if(currentQuestion == examData.length){
            submitProcess();
        }else{
            const longAnswerUser = document.querySelector('.long-answer-input');
            if (longAnswerUser) {
                longAnswerToSave[currentQuestion] = longAnswerUser.value;
                currentQuestion++;
            }else{
                const selectedOption = document.querySelector('.selected');
                selectedOption_list[currentQuestion] = selectedOption.textContent;
                if (selectedOption) {
                    const answer = selectedOption.textContent;
                    incorrectAnswers[currentQuestion] = undefined;
                    if (answer !== examData[currentQuestion].answer) {
                        incorrectAnswers[currentQuestion] = {
                            question: examData[currentQuestion].question,
                            incorrectAnswer: answer
                        };
                    }
                    currentQuestion++;
                    if (currentQuestion < examData.length) {
                        document.querySelectorAll('.option').forEach(btn => {
                            btn.classList.remove('selected');
                            btn.classList.remove('w3-black');
                            btn.classList.remove('w3-hover-black'); 
                        });
                    }
                }
            }
        }
    }

    let answerToSave = [];
    let score = [];
    let question_id = [];

    function submitProcess() {
        examContainer.style.display = 'none';
        submitButton.style.display = 'none';
        flagButton.style.display = 'none';
        readButton.style.display = 'none';
        exitButton.style.display = 'inline-block';

        document.querySelectorAll('.side_bar_question').forEach(function(item) {
            item.style.display = 'none';
        });

        for (let i = 0; i < examData.length; i++) {
            if (incorrectAnswers[i] !== undefined) {
                answerToSave[i] = incorrectAnswers[i].incorrectAnswer;
                score[i] = 0;
            } else if (longAnswerToSave[i] !== undefined) {
                answerToSave[i] = longAnswerToSave[i];
                score[i] = '';
            } else {
                answerToSave[i] = examData[i].answer;
                score[i] = examData[i].mark;
            }
            question_id[i] = examData[i].question_id;
        }

        var dataToSend = {
            answer: answerToSave,
            score: score,
            question_id: question_id
        };

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "saveAnswerExam.php", true);
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    calResult();
                } 
            }
        };
        xhr.send(JSON.stringify(dataToSend));
    }

    function calResult() {
        clearInterval(timer);
        countdownElement.style.color = 'black';
        countdownElement.textContent = "Submitted!";
        var totalResult = 0;
        var hasLongQuestion = false;
        for (let i = 0; i < examData.length; i++) {
            if (!Array.isArray(examData[i].options)) {
                hasLongQuestion = true;
                break;
            }
        }
        if (!hasLongQuestion) {
            for (let i = 0; i < examData.length; i++) {
                if (examData[i].answer == answerToSave[i]) {
                    totalResult = totalResult + parseInt(examData[i].mark);
                }
            }
            
            var dataToSend = {
                totalResult: totalResult
            };

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "saveResultExam.php", true);
            xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
            xhr.send(JSON.stringify(dataToSend));
        }
    }

    function exitConfirmation(){
        if (confirm("Are you sure you want to exit?")) {
            history.back();
        }
    }

    var countdownElement = document.getElementById('countdown');
    let timer; 

    function updateCountdown(endTime) {
        var currentTime = new Date().getTime(); 
        var remainingTime = endTime - currentTime; 

        var hours = Math.floor(remainingTime / (1000 * 60 * 60));
        var minutes = Math.floor((remainingTime % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((remainingTime % (1000 * 60)) / 1000);

        countdownElement.textContent = `Time remaining: ${hours}h ${minutes}m ${seconds}s`;

        if (remainingTime <= 900000) {
            countdownElement.style.color = 'red';
        }

        if (remainingTime <= 0) {
            clearInterval(timer);
            setNullAnswer();
        }
    }

    function setNullAnswer() {
        for (let i = 0; i < examData.length; i++) {
            if (optionLongList[i].longQuestion !== '') {
                if (longAnswerToSave[i] === undefined) {
                    longAnswerToSave[i] = '';
                }
            } else {
                if (selectedOption_list[i] === undefined){
                    incorrectAnswers[i] = {
                        question: examData[i].question,
                        incorrectAnswer: ''
                    };
                }
            }
        }
        submitProcess();
    }

    function startTimer() {
        countdownElement.style.display = "inline-block";
        var startTime = new Date().getTime(); 
        var endTime = startTime + (timelimit * 60 * 1000);
        updateCountdown(endTime); 
        timer = setInterval(function() {
            updateCountdown(endTime);
        }, 1000);
    }
    
    <?php if ($time_limit_min !== null): ?>
        var timelimit = <?php echo $time_limit_min ?>;
        document.addEventListener("DOMContentLoaded", startTimer);
    <?php endif; ?>

    submitButton.addEventListener('click', function() {
        questionDone(currentQuestion);
        checkAnswer();
        setActive(currentQuestion);
    });
    flagButton.addEventListener('click', function() {
        setFlag(currentQuestion)
    });
    exitButton.addEventListener('click', exitConfirmation);

    displaySideBarItem();
    displayQuestion();
</script>