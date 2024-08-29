<script>
    var totalPages_classroom = <?php echo $_SESSION['totalPages_classroom']; ?>;

    document.querySelectorAll('.display_classroom').forEach(item => {
        item.style.display = 'none';
    });

    document.querySelectorAll('.display_classroom').forEach((item, index) => {
        if (index < 9) {
            item.style.display = 'block';
        }
    });

    document.querySelectorAll('.display_classroom_btn').forEach((btn, index) => {
        if(index == 0){
            btn.classList.add('w3-black');
            btn.classList.add('selected');
            btn.classList.remove('w3-hover-black');
            document.querySelector('.prev-display_classroom').disabled = true;
            document.querySelector('.prev-display_classroom').classList.add('disabled'); 
            document.querySelector('.next-display_classroom').disabled = true;
            document.querySelector('.next-display_classroom').classList.add('disabled');
        }
        else{
            document.querySelector('.next-display_classroom').disabled = false;
            document.querySelector('.next-display_classroom').classList.remove('disabled');
        }
        if (index > 4) { 
            btn.style.display = 'none';
        }
    });

    document.querySelectorAll('.display_classroom_btn').forEach(button => {
        button.addEventListener('click', function() {

            document.querySelectorAll('.display_classroom').forEach(item => {
                item.style.display = 'none';
            });

            let pageNo = parseInt(this.textContent);
            let startIndex = (pageNo - 1) * 9;
            let endIndex = startIndex + 8;

            document.querySelectorAll('.display_classroom').forEach((item, index) => {
                if (index >= startIndex && index <= endIndex) {
                    item.style.display = 'block';
                }
            });

            document.querySelectorAll('.display_classroom_btn').forEach(btn => {
                btn.classList.remove('w3-black');
                btn.classList.remove('selected');
                btn.classList.add('w3-hover-black');
            });

            this.classList.add('w3-black');
            this.classList.add('selected');
            this.classList.remove('w3-hover-black');

            var currentNo = parseInt(this.textContent);

            document.querySelector('.prev-display_classroom').disabled = false;
            document.querySelector('.prev-display_classroom').classList.remove('disabled'); 
            document.querySelector('.next-display_classroom').disabled = false;
            document.querySelector('.next-display_classroom').classList.remove('disabled'); 

            if(currentNo == 1){
                document.querySelector('.prev-display_classroom').disabled = true;
                document.querySelector('.prev-display_classroom').classList.add('disabled'); 
            }
            else if(currentNo == totalPages_classroom){
                document.querySelector('.next-display_classroom').disabled = true;
                document.querySelector('.next-display_classroom').classList.add('disabled'); 
            }
            
            if(totalPages_classroom > 5){
                if(currentNo >= 4) {
                    if(currentNo === totalPages_classroom) {
                        document.querySelectorAll('.display_classroom_btn').forEach(item => {
                            item.style.display = 'none';
                        });

                        document.querySelectorAll('.display_classroom_btn').forEach((item, index) => {
                            if (index >= currentNo - 5 && index < currentNo) {
                                item.style.display = 'block';
                            }
                        });
                    }
                    else if(currentNo + 1 === totalPages_classroom) {
                        document.querySelectorAll('.display_classroom_btn').forEach(item => {
                            item.style.display = 'none';
                        });

                        document.querySelectorAll('.display_classroom_btn').forEach((item, index) => {
                            if (index >= currentNo - 4 && index < currentNo + 1) {
                                item.style.display = 'block';
                            }
                        });
                    }
                    else {
                        var currentNo = parseInt(document.querySelector('.display_classroom_btn.selected').textContent);
                        
                        document.querySelectorAll('.display_classroom_btn').forEach(item => {
                            item.style.display = 'none';
                        });

                        document.querySelectorAll('.display_classroom_btn').forEach((item, index) => {
                            if (index >= currentNo - 3 && index < currentNo + 2) {
                                item.style.display = 'block';
                            }
                        });
                    }
                }
                else{
                    if(currentNo === 1 || currentNo === 2) {
                        document.querySelectorAll('.display_classroom_btn').forEach(item => {
                            item.style.display = 'none';
                        });

                        document.querySelectorAll('.display_classroom_btn').forEach((item, index) => {
                            if (index >= 0 && index < 5) {
                                item.style.display = 'block';
                            }
                        });
                    }
                    else {
                        document.querySelectorAll('.display_classroom_btn').forEach(item => {
                            item.style.display = 'none';
                        });

                        document.querySelectorAll('.display_classroom_btn').forEach((item, index) => {
                            if (index >= currentNo - 3 && index < currentNo + 2) {
                                item.style.display = 'block';
                            }
                        });
                    }
                }
            }
        });
    });

    document.querySelector('.prev-display_classroom').addEventListener('click', function() {
        var currentNo = parseInt(document.querySelector('.display_classroom_btn.selected').textContent);

        if (currentNo > 1) {
            let prevNo = currentNo - 1;

            document.querySelectorAll('.display_classroom').forEach(item => {
                item.style.display = 'none';
            });

            let startIndex = (prevNo - 1) * 9;
            let endIndex = startIndex + 8;

            document.querySelectorAll('.display_classroom').forEach((item, index) => {
                if (index >= startIndex && index <= endIndex) {
                    item.style.display = 'block';
                }
            });

            document.querySelectorAll('.display_classroom_btn').forEach(btn => {
                btn.classList.remove('w3-black');
                btn.classList.remove('selected');
                btn.classList.add('w3-hover-black');
            });
            
            let prevBtnIndex = prevNo - 1;
            let prevBtn = document.querySelectorAll('.display_classroom_btn')[prevBtnIndex];
            prevBtn.classList.add('w3-black');
            prevBtn.classList.add('selected');
            prevBtn.classList.remove('w3-hover-black');
            
            document.querySelector('.prev-display_classroom').disabled = false;
            document.querySelector('.prev-display_classroom').classList.remove('disabled'); 
            document.querySelector('.next-display_classroom').disabled = false;
            document.querySelector('.next-display_classroom').classList.remove('disabled'); 

            if(currentNo == 2){
                document.querySelector('.prev-display_classroom').disabled = true;
                document.querySelector('.prev-display_classroom').classList.add('disabled'); 
            }
        }
        else{
            document.querySelector('.prev-display_classroom').disabled = true;
            document.querySelector('.prev-display_classroom').classList.add('disabled'); 
        }

        if(totalPages_classroom > 5){
            if(currentNo >= 4) {
                if(currentNo === totalPages_classroom) {
                    document.querySelectorAll('.display_classroom_btn').forEach(item => {
                        item.style.display = 'none';
                    });

                    document.querySelectorAll('.display_classroom_btn').forEach((item, index) => {
                        if (index >= currentNo - 5 && index < currentNo) {
                            item.style.display = 'block';
                        }
                    });
                }
                else if(currentNo + 1 === totalPages_classroom) {
                    document.querySelectorAll('.display_classroom_btn').forEach(item => {
                        item.style.display = 'none';
                    });

                    document.querySelectorAll('.display_classroom_btn').forEach((item, index) => {
                        if (index >= currentNo - 4 && index < currentNo + 1) {
                            item.style.display = 'block';
                        }
                    });
                }
                else {
                    
                    document.querySelectorAll('.display_classroom_btn').forEach(item => {
                        item.style.display = 'none';
                    });

                    document.querySelectorAll('.display_classroom_btn').forEach((item, index) => {
                        if (index >= currentNo - 3 && index < currentNo + 2) {
                            item.style.display = 'block';
                        }
                    });
                }
            }
            else{
                if(currentNo === 1 || currentNo === 2) {
                    document.querySelectorAll('.display_classroom_btn').forEach(item => {
                        item.style.display = 'none';
                    });

                    document.querySelectorAll('.display_classroom_btn').forEach((item, index) => {
                        if (index >= 0 && index < 5) {
                            item.style.display = 'block';
                        }
                    });
                }
                else {
                    var currentNo = parseInt(document.querySelector('.display_classroom_btn.selected').textContent);
                    
                    document.querySelectorAll('.display_classroom_btn').forEach(item => {
                        item.style.display = 'none';
                    });

                    document.querySelectorAll('.display_classroom_btn').forEach((item, index) => {
                        if (index >= currentNo - 3 && index < currentNo + 2) {
                            item.style.display = 'block';
                        }
                    });
                }
            }
        }
    });

    document.querySelector('.next-display_classroom').addEventListener('click', function() {
        var currentNo = parseInt(document.querySelector('.display_classroom_btn.selected').textContent);

        if (currentNo < totalPages_classroom) {
            let nextNo = currentNo + 1;

            document.querySelectorAll('.display_classroom').forEach(item => {
                item.style.display = 'none';
            });

            let startIndex = (nextNo - 1) * 9;
            let endIndex = startIndex + 8;

            document.querySelectorAll('.display_classroom').forEach((item, index) => {
                if (index >= startIndex && index <= endIndex) {
                    item.style.display = 'block';
                }
            });

            document.querySelectorAll('.display_classroom_btn').forEach(btn => {
                btn.classList.remove('w3-black');
                btn.classList.remove('selected');
                btn.classList.add('w3-hover-black');
            });

            let nextBtnIndex = nextNo - 1;
            let nextBtn = document.querySelectorAll('.display_classroom_btn')[nextBtnIndex];
            nextBtn.classList.add('w3-black');
            nextBtn.classList.add('selected');
            nextBtn.classList.remove('w3-hover-black');

            document.querySelector('.prev-display_classroom').disabled = false;
            document.querySelector('.prev-display_classroom').classList.remove('disabled'); 
            document.querySelector('.next-display_classroom').disabled = false;
            document.querySelector('.next-display_classroom').classList.remove('disabled'); 

            if(currentNo == totalPages_classroom - 1){
                document.querySelector('.next-display_classroom').disabled = true;
                document.querySelector('.next-display_classroom').classList.add('disabled'); 
            }
        }
        else{
            document.querySelector('.next-display_classroom').disabled = true;
            document.querySelector('.next-display_classroom').classList.add('disabled'); 
        }

        if(totalPages_classroom > 5 && currentNo >= 4) {
            if(currentNo === totalPages_classroom) {
                document.querySelectorAll('.display_classroom_btn').forEach(item => {
                    item.style.display = 'none';
                });

                document.querySelectorAll('.display_classroom_btn').forEach((item, index) => {
                    if (index >= currentNo - 5 && index < currentNo) {
                        item.style.display = 'block';
                    }
                });
            }
            else if(currentNo + 1 === totalPages_classroom) {
                document.querySelectorAll('.display_classroom_btn').forEach(item => {
                    item.style.display = 'none';
                });

                document.querySelectorAll('.display_classroom_btn').forEach((item, index) => {
                    if (index >= currentNo - 4 && index < currentNo + 1) {
                        item.style.display = 'block';
                    }
                });
            }
            else {
                document.querySelectorAll('.display_classroom_btn').forEach(item => {
                    item.style.display = 'none';
                });

                document.querySelectorAll('.display_classroom_btn').forEach((item, index) => {
                    if (index >= currentNo - 3 && index < currentNo + 2) {
                        item.style.display = 'block';
                    }
                });
            }
        }
    });
</script>