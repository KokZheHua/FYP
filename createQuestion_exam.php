<?php
    session_start();

    $checkfirstSubmit = $_SESSION['checkfirstSubmit'];
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Create Question</title>
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    </head>
    <body>
        <div class="w3-container w3-padding-64">
            <h2 class="w3-center"><b>Create Question</b></h2>
            <div class="w3-card w3-padding w3-round-large" style="max-width: 600px; margin: 0 auto;">
                <form class="w3-container" action="saveQuestion_exam.php" method="POST">
                    <input type="hidden" name="noFirstSubmit" value="<?php echo ($checkfirstSubmit == true) ? 'yes' : 'no'; ?>">
                    <label class="w3-text">Question Type:</label>
                    <select id="questionType" class="w3-select w3-border w3-margin-bottom" name="questionType">
                        <option value="long">Long Answer</option>
                        <option value="multipleChoice" selected>Multiple Choice</option>
                    </select>

                    <label class="w3-text">Question:</label>
                    <textarea class="w3-input w3-border w3-margin-bottom" name="question" required></textarea>

                    <div id="multipleChoiceOptions">
                        <label class="w3-text">Option 1:</label>
                        <input id="option1" class="w3-input w3-border w3-margin-bottom" type="text" name="option1"  oninput="updateOptionValue('option1')">

                        <label class="w3-text">Option 2:</label>
                        <input id="option2" class="w3-input w3-border w3-margin-bottom" type="text" name="option2"  oninput="updateOptionValue('option2')">

                        <label class="w3-text">Option 3:</label>
                        <input id="option3" class="w3-input w3-border w3-margin-bottom" type="text" name="option3"  oninput="updateOptionValue('option3')">

                        <label class="w3-text">Option 4:</label>
                        <input id="option4" class="w3-input w3-border w3-margin-bottom" type="text" name="option4"  oninput="updateOptionValue('option4')">

                        <label class="w3-text">Correct Answer:</label>
                        <select id="correctAnswer" class="w3-select w3-border w3-margin-bottom" name="correctAnswer" >
                            <option value="" disabled selected>Select correct answer</option>
                            <option id="option1_correct">Option 1</option>
                            <option id="option2_correct">Option 2</option>
                            <option id="option3_correct">Option 3</option>
                            <option id="option4_correct">Option 4</option>
                        </select>
                    </div>

                    <label class="w3-text">Mark:</label>
                    <input class="w3-input w3-border w3-margin-bottom" type="number" name="mark" required>

                    <button class="w3-button w3-black w3-round-large w3-margin-bottom" type="submit">Submit</button>
                    <button onclick="confirmExit()" class="w3-button w3-black w3-round-large w3-margin-bottom" type="button">Close</button>
                </form>
            </div>
        </div>

        <script>
            window.onload = function() {
                var option1Input = document.getElementById('option1');
                var option2Input = document.getElementById('option2');
                var option3Input = document.getElementById('option3');
                var option4Input = document.getElementById('option4'); 
                var questionType = document.getElementById('questionType');
                var multipleChoiceOptions = document.getElementById('multipleChoiceOptions');

                var correctAnswerSelect = document.getElementById('correctAnswer');

                if (questionType.value === 'multipleChoice') {
                    multipleChoiceOptions.style.display = 'block';
                    option1Input.setAttribute('required', 'required');
                    option2Input.setAttribute('required', 'required');
                    option3Input.setAttribute('required', 'required');
                    option4Input.setAttribute('required', 'required');
                    correctAnswerSelect.setAttribute('required', true);
                } else {
                    multipleChoiceOptions.style.display = 'none';
                    option1Input.removeAttribute('required');
                    option2Input.removeAttribute('required');
                    option3Input.removeAttribute('required');
                    option4Input.removeAttribute('required');
                    option1Input.value = '';
                    option2Input.value = '';
                    option3Input.value = '';
                    option4Input.value = '';
                    correctAnswerSelect.removeAttribute('required');
                }
            };

            function updateOptionValue(optionId) {
                var inputValue = document.getElementById(optionId).value;
                var selectOption = document.querySelector('option[id="' + optionId + '_correct"]');
                selectOption.textContent = inputValue;
                selectOption.value = inputValue;
            }

            document.getElementById('questionType').addEventListener('change', function() {
                var multipleChoiceOptions = document.getElementById('multipleChoiceOptions');
                
                var correctAnswerSelect = document.getElementById('correctAnswer');
                var option1Input = document.getElementById('option1');
                var option2Input = document.getElementById('option2');
                var option3Input = document.getElementById('option3');
                var option4Input = document.getElementById('option4'); 

                if (this.value === 'multipleChoice') {
                    multipleChoiceOptions.style.display = 'block';
                    option1Input.setAttribute('required', 'required');
                    option2Input.setAttribute('required', 'required');
                    option3Input.setAttribute('required', 'required');
                    option4Input.setAttribute('required', 'required');
                    correctAnswerSelect.setAttribute('required', true);
                } else {
                    multipleChoiceOptions.style.display = 'none';
                    option1Input.removeAttribute('required');
                    option2Input.removeAttribute('required');
                    option3Input.removeAttribute('required');
                    option4Input.removeAttribute('required');
                    option1Input.value = '';
                    option2Input.value = '';
                    option3Input.value = '';
                    option4Input.value = '';
                    correctAnswerSelect.removeAttribute('required');
                }
            });

            function confirmExit() {
                var confirmExit = confirm("Are you sure you want to exit?");

                if (confirmExit) {
                    window.location.href = 'classroom_teacher.php?classroom=<?php echo $_SESSION['abbreviation']; ?>';
                }
            }


        </script>

    </body>
</html>