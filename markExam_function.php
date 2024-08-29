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
    $optionsArray = "";
    $user_answer = [];
    $user_question_mark = [];

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

            $query = "SELECT user_answer_exam, score_mark FROM exam_question WHERE question_id = '$question_id' AND exam_id = '$exam_id' AND student_classroom_id = '$student_classroom_id'";
            $result = mysqli_query($con, $query);
            if(mysqli_num_rows($result) > 0){
                $row = mysqli_fetch_assoc($result);
                if($row['user_answer_exam'] !== null){
                    $user_answer[] = $row['user_answer_exam'];
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
    const submitMarkButton = document.getElementById('submitMarkBtn');
    const submitResultButton = document.getElementById('submitResultBtn');
    const exitButton = document.getElementById('exit');
    const markInput = document.getElementById('markInput');

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

    function displaySummary() {
        const titleElement = document.getElementById('titleQuiz');
        titleElement.textContent = 'Summary';

        submitMarkButton.style.display = "none";
        document.getElementById("inputMark").style.display = "none";
        submitResultButton.style.display = "inline-block";
        
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

    function displayQuestion() {
        submitMarkButton.style.display = "inline-block";
        document.getElementById("inputMark").style.display = "inline-block";
        submitResultButton.style.display = "none";

        const titleElement = document.getElementById('titleQuiz');
        titleElement.textContent = 'Question';
        
        const questionData = quizData[currentQuestion];
        const questionAnswerData = userAnswerData[currentQuestion];

        const questionElement = document.createElement('div');
        questionElement.className = 'question';
        questionElement.innerHTML = (currentQuestion + 1) + ". " + questionData.question + " (" + questionData.mark + "marks)";

        markInput.max = questionData.mark;

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

    function submitResult(){
        var dataToSend = {
            totalResult: totalMark
        };

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "saveResultExam.php", true);
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) { 
                if (xhr.status === 200) {
                    alert("Result submitted.");
                } 
            }
        };
        xhr.send(JSON.stringify(dataToSend));
    }

    function submitMark() {
        var mark = document.getElementById('markInput').value;
        if (mark.trim() === '') {
            alert('Please enter a mark before submitting.');
            return;
        }
        var current_question_id = quizData[currentQuestion].question_id;
        var current_exam_id = <?php echo $exam_id ?>;
        var current_student_classroom_id = <?php echo $student_classroom_id ?>;
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'submitMark_exam.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                alert(xhr.responseText);
                location.reload();
            }
        };
        xhr.send('exam_id=' + encodeURIComponent(current_exam_id) + '&question_id=' + encodeURIComponent(current_question_id) + '&student_classroom_id=' + encodeURIComponent(current_student_classroom_id) + '&mark=' + encodeURIComponent(mark));
    }

    markInput.addEventListener('input', function() {
        var currentValue = parseInt(markInput.value);

        var maxLimit = parseInt(markInput.max);

        if (currentValue > maxLimit) {
            markInput.value = maxLimit;
        }
    });


    submitMarkButton.addEventListener('click', submitMark);
    submitResultButton.addEventListener('click', submitResult);
    exitButton.addEventListener('click', exitConfirmation);

    displaySideBarItem();
    displayQuestion();
</script>