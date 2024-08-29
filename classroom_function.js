function open_side_bar() {
    document.getElementById("mySidebar").style.display = "block";
    document.getElementById("side_bar").classList.add("w3-black");
    document.querySelector(".w3-main").style.marginLeft = "300px"; 
}

function close_side_bar() {
    document.getElementById("mySidebar").style.display = "none";
    document.getElementById("side_bar").classList.remove("w3-black");
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

function setActive(id) {
    document.querySelectorAll('.side_bar_item').forEach(item => {
        if (item.id === id) {
            item.classList.add('w3-text-teal');
        } else {
            item.classList.remove('w3-text-teal');
        }
    });
}

function filterBtn(btnSelected){
    document.querySelectorAll('.filter_btn').forEach(item => {
        if (item.classList.contains('w3-black')) {
            item.classList.remove('w3-black');
            item.classList.add('w3-white');
        }
    });
    
    document.querySelectorAll('.filter_btn').forEach(item => {
        if (item.id === btnSelected) {
            item.classList.remove('w3-white');
            item.classList.add('w3-black');
        }
    });
}

function displayList(list_to_display){
    document.querySelectorAll('.item_list').forEach(item => {
        if (item.style.display = 'block') {
            item.style.display = 'none';
        }
    });
    if(list_to_display != null){
        document.querySelectorAll('.item_list').forEach(item => {
            if (item.id === list_to_display) {
                item.style.display = 'block';
            }
        });
    }
    else{
        document.querySelectorAll('.item_list').forEach(item => {
            item.style.display = 'block';
        });
    }
}

function startCountdown(remainingTime, linkID) {
    var countdownID = linkID + '_countdown';
    var countdown = document.getElementById(countdownID);
    var link = document.getElementById(linkID);
    
    var timer = setInterval(function() {
        if (remainingTime <= 0) {
            clearInterval(timer);
            countdown.innerHTML = "Cannot access the quizz.";
            link.style.pointerEvents = "none";
        } else {
            var days = Math.floor(remainingTime / (24 * 3600));
            var hours = Math.floor((remainingTime % (24 * 3600)) / 3600);
            var minutes = Math.floor((remainingTime % 3600) / 60);
            var seconds = remainingTime % 60;
            var display = "";
            if (days > 0) {
                display += days + "d ";
            }
            display += hours + "h " + minutes + "m " + seconds + "s";
            countdown.innerHTML = "Time left: " + display;
            remainingTime--;
        }
    }, 1000);
}
