<script>
    var totalPages_recently = <?php echo $_SESSION['totalPages_recently']; ?>;

    document.querySelectorAll('.recently_accessed').forEach(item => {
        item.style.display = 'none';
    });

    document.querySelectorAll('.recently_accessed').forEach((item, index) => {
        if (index < 3) {
            item.style.display = 'block';
        }
    });

    document.querySelectorAll('.recently_btn').forEach((btn, index) => {
        if(index == 0){
            btn.classList.add('w3-black');
            btn.classList.add('recently_selected');
            btn.classList.remove('w3-hover-black');
            document.querySelector('.prev-recently').disabled = true;
            document.querySelector('.prev-recently').classList.add('disabled'); 
            document.querySelector('.next-recently').disabled = true;
            document.querySelector('.next-recently').classList.add('disabled');
        }
        else{
            document.querySelector('.next-recently').disabled = false;
            document.querySelector('.next-recently').classList.remove('disabled');
        }
        if (index > 4) { 
            btn.style.display = 'none';
        }
    });

    document.querySelectorAll('.recently_btn').forEach(button => {
        button.addEventListener('click', function() {

            document.querySelectorAll('.recently_accessed').forEach(item => {
                item.style.display = 'none';
            });

            let pageNo = parseInt(this.textContent);
            let startIndex = (pageNo - 1) * 3;
            let endIndex = startIndex + 2;

            document.querySelectorAll('.recently_accessed').forEach((item, index) => {
                if (index >= startIndex && index <= endIndex) {
                    item.style.display = 'block';
                }
            });

            document.querySelectorAll('.recently_btn').forEach(btn => {
                btn.classList.remove('w3-black');
                btn.classList.remove('recently_selected');
                btn.classList.add('w3-hover-black');
            });

            this.classList.add('w3-black');
            this.classList.add('recently_selected');
            this.classList.remove('w3-hover-black');

            var currentNo = parseInt(this.textContent);

            document.querySelector('.prev-recently').disabled = false;
            document.querySelector('.prev-recently').classList.remove('disabled'); 
            document.querySelector('.next-recently').disabled = false;
            document.querySelector('.next-recently').classList.remove('disabled'); 

            if(currentNo == 1){
                document.querySelector('.prev-recently').disabled = true;
                document.querySelector('.prev-recently').classList.add('disabled'); 
            }
            else if(currentNo == totalPages_recently){
                document.querySelector('.next-recently').disabled = true;
                document.querySelector('.next-recently').classList.add('disabled'); 
            }

            if(totalPages_recently > 5){
                if(currentNo >= 4) {
                    if(currentNo === totalPages_recently) {
                        document.querySelectorAll('.recently_btn').forEach(item => {
                            item.style.display = 'none';
                        });

                        document.querySelectorAll('.recently_btn').forEach((item, index) => {
                            if (index >= currentNo - 5 && index < currentNo) {
                                item.style.display = 'block';
                            }
                        });
                    }
                    else if(currentNo + 1 === totalPages_recently) {
                        document.querySelectorAll('.recently_btn').forEach(item => {
                            item.style.display = 'none';
                        });

                        document.querySelectorAll('.recently_btn').forEach((item, index) => {
                            if (index >= currentNo - 4 && index < currentNo + 1) {
                                item.style.display = 'block';
                            }
                        });
                    }
                    else {
                        var currentNo = parseInt(document.querySelector('.recently_selected').textContent);
                        
                        document.querySelectorAll('.recently_btn').forEach(item => {
                            item.style.display = 'none';
                        });

                        document.querySelectorAll('.recently_btn').forEach((item, index) => {
                            if (index >= currentNo - 3 && index < currentNo + 2) {
                                item.style.display = 'block';
                            }
                        });
                    }
                }
                else{
                    if(currentNo === 1 || currentNo === 2) {
                        document.querySelectorAll('.recently_btn').forEach(item => {
                            item.style.display = 'none';
                        });

                        document.querySelectorAll('.recently_btn').forEach((item, index) => {
                            if (index >= 0 && index < 5) {
                                item.style.display = 'block';
                            }
                        });
                    }
                    else {
                        document.querySelectorAll('.recently_btn').forEach(item => {
                            item.style.display = 'none';
                        });

                        document.querySelectorAll('.recently_btn').forEach((item, index) => {
                            if (index >= currentNo - 3 && index < currentNo + 2) {
                                item.style.display = 'block';
                            }
                        });
                    }
                }
            }
        });
    });

    document.querySelector('.prev-recently').addEventListener('click', function() {
        var currentNo = parseInt(document.querySelector('.recently_selected').textContent);

        if (currentNo > 1) {
            let prevNo = currentNo - 1;

            document.querySelectorAll('.recently_accessed').forEach(item => {
                item.style.display = 'none';
            });

            let startIndex = (prevNo - 1) * 3;
            let endIndex = startIndex + 2;

            document.querySelectorAll('.recently_accessed').forEach((item, index) => {
                if (index >= startIndex && index <= endIndex) {
                    item.style.display = 'block';
                }
            });

            document.querySelectorAll('.recently_btn').forEach(btn => {
                btn.classList.remove('w3-black');
                btn.classList.remove('recently_selected');
                btn.classList.add('w3-hover-black');
            });
            
            let prevBtnIndex = prevNo - 1;
            let prevBtn = document.querySelectorAll('.recently_btn')[prevBtnIndex];
            prevBtn.classList.add('w3-black');
            prevBtn.classList.add('recently_selected');
            prevBtn.classList.remove('w3-hover-black');

            document.querySelector('.prev-recently').disabled = false;
            document.querySelector('.prev-recently').classList.remove('disabled'); 
            document.querySelector('.next-recently').disabled = false;
            document.querySelector('.next-recently').classList.remove('disabled'); 

            if(currentNo == 2){
                document.querySelector('.prev-recently').disabled = true;
                document.querySelector('.prev-recently').classList.add('disabled'); 
            }
        }
        else{
            document.querySelector('.prev-recently').disabled = true;
            document.querySelector('.prev-recently').classList.add('disabled'); 
        }

        if(totalPages_recently > 5){
            if(currentNo >= 4) {
                if(currentNo === totalPages_recently) {
                    document.querySelectorAll('.recently_btn').forEach(item => {
                        item.style.display = 'none';
                    });

                    document.querySelectorAll('.recently_btn').forEach((item, index) => {
                        if (index >= currentNo - 5 && index < currentNo) {
                            item.style.display = 'block';
                        }
                    });
                }
                else if(currentNo + 1 === totalPages_recently) {
                    document.querySelectorAll('.recently_btn').forEach(item => {
                        item.style.display = 'none';
                    });

                    document.querySelectorAll('.recently_btn').forEach((item, index) => {
                        if (index >= currentNo - 4 && index < currentNo + 1) {
                            item.style.display = 'block';
                        }
                    });
                }
                else {
                    
                    document.querySelectorAll('.recently_btn').forEach(item => {
                        item.style.display = 'none';
                    });

                    document.querySelectorAll('.recently_btn').forEach((item, index) => {
                        if (index >= currentNo - 3 && index < currentNo + 2) {
                            item.style.display = 'block';
                        }
                    });
                }
            }
            else{
                if(currentNo === 1 || currentNo === 2) {
                    document.querySelectorAll('.recently_btn').forEach(item => {
                        item.style.display = 'none';
                    });

                    document.querySelectorAll('.recently_btn').forEach((item, index) => {
                        if (index >= 0 && index < 5) {
                            item.style.display = 'block';
                        }
                    });
                }
                else {
                    var currentNo = parseInt(document.querySelector('.recently_selected').textContent);
                    
                    document.querySelectorAll('.recently_btn').forEach(item => {
                        item.style.display = 'none';
                    });

                    document.querySelectorAll('.recently_btn').forEach((item, index) => {
                        if (index >= currentNo - 3 && index < currentNo + 2) {
                            item.style.display = 'block';
                        }
                    });
                }
            }
        }
    });

    document.querySelector('.next-recently').addEventListener('click', function() {
        var currentNo = parseInt(document.querySelector('.recently_selected').textContent);

        if (currentNo < totalPages_recently) {
            let nextNo = currentNo + 1;

            document.querySelectorAll('.recently_accessed').forEach(item => {
                item.style.display = 'none';
            });

            let startIndex = (nextNo - 1) * 3;
            let endIndex = startIndex + 2;

            document.querySelectorAll('.recently_accessed').forEach((item, index) => {
                if (index >= startIndex && index <= endIndex) {
                    item.style.display = 'block';
                }
            });

            document.querySelectorAll('.recently_btn').forEach(btn => {
                btn.classList.remove('w3-black');
                btn.classList.remove('recently_selected');
                btn.classList.add('w3-hover-black');
            });

            let nextBtnIndex = nextNo - 1;
            let nextBtn = document.querySelectorAll('.recently_btn')[nextBtnIndex];
            nextBtn.classList.add('w3-black');
            nextBtn.classList.add('recently_selected');
            nextBtn.classList.remove('w3-hover-black');

            document.querySelector('.prev-recently').disabled = false;
            document.querySelector('.prev-recently').classList.remove('disabled'); 
            document.querySelector('.next-recently').disabled = false;
            document.querySelector('.next-recently').classList.remove('disabled'); 

            if(currentNo == totalPages_recently - 1){
                document.querySelector('.next-recently').disabled = true;
                document.querySelector('.next-recently').classList.add('disabled'); 
            }
        }
        else{
            document.querySelector('.next-recently').disabled = true;
            document.querySelector('.next-recently').classList.add('disabled'); 
        }

        if(totalPages_recently > 5 && currentNo >= 4) {
            if(currentNo === totalPages_recently) {
                document.querySelectorAll('.recently_btn').forEach(item => {
                    item.style.display = 'none';
                });

                document.querySelectorAll('.recently_btn').forEach((item, index) => {
                    if (index >= currentNo - 5 && index < currentNo) {
                        item.style.display = 'block';
                    }
                });
            }
            else if(currentNo + 1 === totalPages_recently) {
                document.querySelectorAll('.recently_btn').forEach(item => {
                    item.style.display = 'none';
                });

                document.querySelectorAll('.recently_btn').forEach((item, index) => {
                    if (index >= currentNo - 4 && index < currentNo + 1) {
                        item.style.display = 'block';
                    }
                });
            }
            else {
                document.querySelectorAll('.recently_btn').forEach(item => {
                    item.style.display = 'none';
                });

                document.querySelectorAll('.recently_btn').forEach((item, index) => {
                    if (index >= currentNo - 3 && index < currentNo + 2) {
                        item.style.display = 'block';
                    }
                });
            }
        }
    });
</script>