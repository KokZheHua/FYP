<?php 

    $quizz_id = $_SESSION['quizz_id'];

    $student_classroom_id = $_GET['student_classroom_id'];

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
    $optionsArray = "";
    $user_answer = [];
    $user_question_mark = [];

    $query = "SELECT question_id FROM quizz_question WHERE quizz_id = '$quizz_id' AND student_classroom_id = '$student_classroom_id'";
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

            $query = "SELECT user_answer_quizz, score_mark FROM quizz_question WHERE question_id = '$question_id' AND quizz_id = '$quizz_id' AND student_classroom_id = '$student_classroom_id'";
            $result = mysqli_query($con, $query);
            if(mysqli_num_rows($result) > 0){
                $row = mysqli_fetch_assoc($result);
                if($row['user_answer_quizz'] !== null){
                    $user_answer[] = $row['user_answer_quizz'];
                }else{
                    $user_answer[] = '';
                }
                $user_question_mark[] = $row['score_mark'];
            }
        }
    }
    mysqli_close($con);
?>

<script>
    const quizData = [];
    const userAnswerData = [];
    const userMarkData = [];
    <?php for ($i = 0; $i < $totalItems; $i++) : ?>
        <?php if ($optionLong[$i] !== null) : ?>
        <?php  $longAnswer = $optionLong[$i];?>
            quizData.push({
                question: '<?php echo $question_title[$i] ?>',
                options: '<?php echo $longAnswer ?>',
                answer: '<?php echo $answer[$i] ?>',
                mark: '<?php echo $mark[$i] ?>'
            });
        <?php else : ?>
            quizData.push({
                question: '<?php echo $question_title[$i] ?>',
                options: ['<?php echo $optionA[$i] ?>', '<?php echo $optionB[$i] ?>', '<?php echo $optionC[$i] ?>', '<?php echo $optionD[$i] ?>'],
                answer: '<?php echo $answer[$i] ?>',
                mark: '<?php echo $mark[$i] ?>'
            });
        <?php endif;?>
        userAnswerData.push({
            answer: '<?php echo $user_answer[$i] ?>',
        });
        
        userMarkData.push({
            mark: '<?php echo $user_question_mark[$i] ?>',
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

    const sideBarContainer = document.getElementById('question_sideBar');
    const quizContainer = document.getElementById('quiz');
    const exitButton = document.getElementById('exit');

    let currentQuestion = 0;
    let totalMCQ = 0;
    let totalMCQMark = 0;
    let totalMark = 0;
    var hasAllLongAnswerMark = true;
    for (let i = 0; i < userMarkData.length; i++) {
        if(userMarkData[i].mark === ''){
            hasAllLongAnswerMark = false;
        }else{
            totalMark = totalMark + parseInt(userMarkData[i].mark);
        }
    }

    var readButton = document.getElementById("read");

    function displaySummary() {
        readButton.style.display = 'none';
        const titleElement = document.getElementById('titleQuiz');
        titleElement.textContent = 'Summary';
        
        const summaryMCQ = document.createElement('p');
        summaryMCQ.textContent = 'MCQ Mark : ' + totalMCQMark + ' (in ' + totalMCQ + ' Multiple choice questions)';
        
        quizContainer.innerHTML = '';
        quizContainer.appendChild(summaryMCQ);

        const summaryLongQuestion = document.createElement('p');

        let totalLongQuestion = quizData.length - totalMCQ;
        
        if (hasAllLongAnswerMark === false){
            summaryLongQuestion.textContent = ' Long Answer Mark : Not mark yet. (in ' + totalLongQuestion + ' Long answer questions)';
        }else{
            let totalLongQuestionMark = totalMark - totalMCQMark;

            summaryLongQuestion.textContent = ' Long Answer Mark : ' + totalLongQuestionMark + ' (in ' + totalLongQuestion + ' Long answer questions)';
        }

        quizContainer.appendChild(summaryLongQuestion);

        const summaryTotalMark = document.createElement('p');
        let totalQuestion = totalMCQ + totalLongQuestion;
        summaryTotalMark.textContent = ' Total Mark : ' + totalMark + ' (in ' + totalQuestion + ' Total questions)';

        quizContainer.appendChild(summaryTotalMark);
    }

    function setActive(id) {
        event.preventDefault();
        document.querySelectorAll('.side_bar_item').forEach(item => {
            if (item.id == id) {
                item.classList.add('w3-black');
                currentQuestion = parseInt(id);
                if (currentQuestion == quizData.length) {
                    displaySummary();
                }else{
                    displayQuestion();
                }
            } else {
                item.classList.remove('w3-black');
            }
        });
    }

    function displaySideBarItem() {
        for (var i = 0; i <= quizData.length; i++) {
            if(i == quizData.length){
                var link = document.createElement('a');
                link.href = '#';
                link.onclick = function() { 
                    setActive(this.id); 
                };
                link.id = i;
                link.classList.add('side_bar_item', 'w3-bar-item', 'w3-button', 'w3-padding');
                link.textContent = "Summary";
            }else{
                var link = document.createElement('a');
                link.href = '#';
                link.onclick = function() { 
                    setActive(this.id); 
                };
                link.id = i;
                link.classList.add('side_bar_item', 'side_bar_question', 'w3-bar-item', 'w3-button', 'w3-padding');
                if (i === 0) {
                    link.classList.add('w3-black'); 
                }
                link.textContent = "Question" + (i + 1);
            }

            sideBarContainer.appendChild(link);
        }
        document.querySelectorAll('.side_bar_question').forEach((item, index) => {
            if(Array.isArray(quizData[index].options)){
                totalMCQ++;
                if(quizData[index].answer == userAnswerData[index].answer){
                    item.classList.add('correctAnswer');
                    var tickIcon = document.createElement('i');
                    tickIcon.textContent = '\u2714';
                    item.insertBefore(tickIcon, item.firstChild);
                    totalMCQMark = totalMCQMark + parseInt(quizData[index].mark);
                }else{
                    item.classList.add('wrongAnswer');
                    var crossIcon = document.createElement('i');
                    crossIcon.classList.add('crossIcon');
                    crossIcon.innerHTML = '&#x2715;';
                    crossIcon.style.color = 'black';
                    item.insertBefore(crossIcon, item.firstChild);
                }
            }
        });
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
        readButton.style.display = 'inline-block';
        const titleElement = document.getElementById('titleQuiz');
        titleElement.textContent = 'Question';
        
        const questionData = quizData[currentQuestion];
        const questionAnswerData = userAnswerData[currentQuestion];

        const questionElement = document.createElement('div');
        questionElement.className = 'question';
        questionElement.innerHTML = (currentQuestion + 1) + ". " + questionData.question + " (" + questionData.mark + "marks)";

        const optionsElement = document.createElement('div');
        optionsElement.className = 'options';

        const markElement = document.createElement('p');

        if (!Array.isArray(questionData.options)) {
            const longAnswerInput = document.createElement('textarea');
            longAnswerInput.placeholder = 'Enter your answer here...';
            longAnswerInput.name = 'long_answer';
            longAnswerInput.className = 'long-answer-input';
            longAnswerInput.readOnly = true;
            if(questionAnswerData.answer !== null){
                longAnswerInput.value = questionAnswerData.answer;
            }else{
                longAnswerInput.value = '';
            }
            optionsElement.appendChild(longAnswerInput);

            if (hasAllLongAnswerMark === false) {
                markElement.textContent = 'Mark : Not marked yet.';
            } else {
                markElement.textContent = 'Mark : ' + userMarkData[currentQuestion].mark;
            }
        }else{
            for (let i = 0; i < questionData.options.length; i++) {
                const button = document.createElement('button');
                button.textContent = questionData.options[i];
                button.classList.add('option', 'w3-button', 'w3-padding-large', 'w3-white', 'w3-border');
                
                optionsElement.appendChild(button);
            }
            markElement.textContent = ' Mark : ' + userMarkData[currentQuestion].mark;
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

        quizContainer.innerHTML = '';
        quizContainer.appendChild(questionElement);
        quizContainer.appendChild(optionsElement);
        quizContainer.appendChild(markElement);
        
        document.querySelectorAll('.option').forEach(btn => {
            if(btn.textContent == questionData.answer){
                btn.classList.add('correctAnswer');
                var tickIcon = document.createElement('i');
                tickIcon.textContent = '\u2714';
                btn.insertBefore(tickIcon, btn.firstChild);
            }
        });
        if(questionAnswerData.answer !== ''){
            if(questionAnswerData.answer !== questionData.answer){   
                document.querySelectorAll('.option').forEach(btn => {
                    if(btn.textContent == questionAnswerData.answer){
                        btn.classList.add('wrongAnswer');
                        var crossIcon = document.createElement('i');
                        crossIcon.classList.add('crossIcon');
                        crossIcon.innerHTML = '&#x2715;';
                        crossIcon.style.color = 'black';
                        btn.insertBefore(crossIcon, btn.firstChild);
                    }
                });
            }
        }
    }

    function exitConfirmation(){
        if (confirm("Are you sure you want to exit?")) {
            history.back();
        }
    }

    exitButton.addEventListener('click', exitConfirmation);

    displaySideBarItem();
    displayQuestion();
</script>