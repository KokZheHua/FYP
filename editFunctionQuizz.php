<?php 
    
    $quizz_id = $_SESSION['quizz_id'];

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
    $totalItems = 0;

    $query = "SELECT DISTINCT question_id FROM quizz_question WHERE quizz_id = '$quizz_id'";
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
                    $optionA[] = '';
                    $optionB[] = '';
                    $optionC[] = '';
                    $optionD[] = '';
                }else{
                    $optionLong[] = null;
                    $optionsArray = explode('/~/', $row['options']);
                    $optionA[] = isset($optionsArray[0]) ? $optionsArray[0] : '';
                    $optionB[] = isset($optionsArray[1]) ? $optionsArray[1] : '';
                    $optionC[] = isset($optionsArray[2]) ? $optionsArray[2] : '';
                    $optionD[] = isset($optionsArray[3]) ? $optionsArray[3] : '';
                }
            }

            $query = "SELECT user_answer_quizz, score_mark FROM quizz_question WHERE question_id = '$question_id' AND quizz_id = '$quizz_id'";
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
    <?php for ($i = 0; $i < $totalItems; $i++) : ?>
        <?php if ($optionLong[$i] !== null) : ?>
        <?php  $longAnswer = $optionLong[$i];?>
            quizData.push({
                question_id: '<?php echo $question_id_list[$i] ?>',
                question: '<?php echo $question_title[$i] ?>',
                options: '<?php echo $longAnswer ?>',
                answer: '<?php echo $answer[$i] ?>',
                mark: '<?php echo $mark[$i] ?>'
            });
        <?php else : ?>
            quizData.push({
                question_id: '<?php echo $question_id_list[$i] ?>',
                question: '<?php echo $question_title[$i] ?>',
                options: ['<?php echo $optionA[$i] ?>', '<?php echo $optionB[$i] ?>', '<?php echo $optionC[$i] ?>', '<?php echo $optionD[$i] ?>'],
                answer: '<?php echo $answer[$i] ?>',
                mark: '<?php echo $mark[$i] ?>'
            });
        <?php endif;?>

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
    const removeButton = document.getElementById('remove');
    const editButton = document.getElementById('edit');

    let currentQuestion = 0;
    let totalMCQ = 0;
    let totalMCQMark = 0;
    let totalMark = 0;

    function addNewQuestion() {
        <?php
            $_SESSION['quizz_id_new'] = $quizz_id;
            $_SESSION['quizzTitle'] = 0;
            $_SESSION['quizzDescription'] = 0;
            $_SESSION['timeLimit'] = 0;
            $_SESSION['startTime'] = 0;
            $_SESSION['endTime'] = 0;
        ?>
        window.location.href='createQuestion_edit.php';
    }

    function setActive(id) {
        event.preventDefault();
        document.querySelectorAll('.side_bar_item').forEach(item => {
            if (item.id == id) {
                item.classList.add('w3-black');
                currentQuestion = parseInt(id);
                if (currentQuestion == quizData.length) {
                    addNewQuestion();
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
                link.textContent = "Add New Question";
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
    }

    var answer_e = null;

    function displayQuestion() {
        const titleElement = document.getElementById('titleQuiz');
        titleElement.textContent = 'Question';
        
        const questionData = quizData[currentQuestion];

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
            longAnswerInput.value = '';
            optionsElement.appendChild(longAnswerInput);
        }else{
            var option_list = []; 
            for (let i = 0; i < questionData.options.length; i++) {
                const button = document.createElement('button');
                button.textContent = questionData.options[i];
                option_list.push(questionData.options[i]); 

                button.classList.add('option', 'w3-button', 'w3-padding-large', 'w3-white', 'w3-border');
                
                optionsElement.appendChild(button);
            }
        }

        quizContainer.innerHTML = '';
        quizContainer.appendChild(questionElement);
        quizContainer.appendChild(optionsElement);
        quizContainer.appendChild(markElement);
        
        answer_e = null;

        document.querySelectorAll('.option').forEach(btn => {
            if(btn.textContent == questionData.answer){
                btn.classList.add('correctAnswer');
                var tickIcon = document.createElement('i');
                tickIcon.textContent = '\u2714';
                btn.insertBefore(tickIcon, btn.firstChild);
                answer_e = btn.textContent;
            }
        });
    }

    function exitConfirmation(){
        if (confirm("Are you sure you want to exit?")) {
            window.location.href = 'classroom_teacher.php?classroom=<?php echo $_SESSION['abbreviation']; ?>';
        }
    }

    function removeConfirmation(){
        const questionData = quizData[currentQuestion];
        const question_id = questionData.question_id;
        if (confirm("Are you sure you want to remove the question?")) {
            window.location.href = 'removeQuestion_quizz.php?question_id=' + question_id;
        }
    }

    function editQuestion() {
        const questionData = quizData[currentQuestion];
        const question_id = questionData.question_id;
        const question = questionData.question;
        const mark = questionData.mark;
        const answer_edit = answer_e;
        if (answer_edit !== null){
            const option_list = questionData.options;
            const option1 = option_list[0];
            const option2 = option_list[1];
            const option3 = option_list[2];
            const option4 = option_list[3];
            const queryString = `?question_id=${encodeURIComponent(question_id)}&question=${encodeURIComponent(question)}&mark=${encodeURIComponent(mark)}&answer_edit=${encodeURIComponent(answer_edit)}&option1=${encodeURIComponent(option1)}&option2=${encodeURIComponent(option2)}&option3=${encodeURIComponent(option3)}&option4=${encodeURIComponent(option4)}`;
            console.log(queryString);
            window.location.href = 'editQuestion.php' + queryString;
        }else{
            const queryString = `?question_id=${encodeURIComponent(question_id)}&question=${encodeURIComponent(question)}&mark=${encodeURIComponent(mark)}&answer_edit=${encodeURIComponent(answer_edit)}`;
            window.location.href = 'editQuestion.php' + queryString;
        }
    }




    exitButton.addEventListener('click', exitConfirmation);
    removeButton.addEventListener('click', removeConfirmation);
    editButton.addEventListener('click', editQuestion);

    displaySideBarItem();
    displayQuestion();
</script>