<?php
    if (!empty($_SESSION['user_id'])) {
        header("Location: dashboard.php");
    }
?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Habit Tracker</title>

    <?php include("inc/css.php"); ?>
    <link rel="stylesheet" href="/css/aos.css">
    
    <style>
    #section-one {
        height: 100vh;
    }

    #section-one h1 {
        font-size: 4rem;
    }

    #section-one h6 {
        font-size: 1.2rem;
    }

    #section-two {
        background-image: linear-gradient(to bottom right, #5AB9EA, #8860D0);
        /*background: #5680E9;*/
        padding: 5rem 0;
        border-top: 10px solid #84CEEB;
        border-bottom: 10px solid #84CEEB;
    }

    #section-two h1 {
        color: black;
    }

    #section-two h3 {
        text-align: center;
        color: black;
        margin-bottom: 1rem;
        border-bottom: 3px solid black;
    }

    #section-two p {
        background: white;
        padding: 10px 15px;
        color: black;
        border-radius: 5px;
    }

    #section-two .bucket-img {
        width: 90%;
    }
    </style>
</head>
<body>
    <?php include("inc/navbar.php");?>
    <div id="section-one" class="d-flex justify-content-center align-items-center flex-column w-100">
        <h1 data-aos="fade-in" data-aos-duration="2000">A NEW WAY TO DEVELOP HABITS</h1>
        <h6 class="mb-4" data-aos="fade-in" data-aos-duration="2000" data-aos-delay="500">Revolutionize the way you look at self growth.</h6>
        <a class="btn" data-aos="fade-in" data-aos-duration="2000" data-aos-delay="500" href="signup.php">Sign Up Today</a>
    </div>

    <div id="section-two">
        <div class="container">
            <h1 class="text-center mb-5" data-aos="fade-in" data-aos-duration="3000">What is Habit Tracker?</h1>
            <div class="row">
                <div class="col-6 mb-5" data-aos="fade-right" data-aos-duration="2000">
                    <div class="w-100">
                        <h3>NEW WAYS OF MOTIVATION</h3>
                        <div class="row">
                            <div class="col-4 x-flex">
                                <img class="bucket-img"  src="/images/brain.png">
                            </div>
                            <div class="col-8 x-flex">
                                <p>The bread and butter of Habit Tracker. Positive reinforcement, behavioural psychology, S.M.A.R.T. goals. All of the modern science tools to keep you moving forward.</p>
                            </div>
                        </div>
                    </div>                        
                </div>
                <div class="col-6 mb-5" data-aos="fade-left" data-aos-duration="2000">
                    <div class="w-100">   
                        <h3>SET HABITS, COMPLETE TASKS, CHANGE YOUR IDENTITY</h3>
                        <div class="row">
                            <div class="col-4 x-flex">
                                <img class="bucket-img"  src="/images/tree.png">
                            </div>
                            <div class="col-8 x-flex">
                                <p>Set the habits you want to build.<br>Complete your daily tasks.<br>Start small, finish big.</p>
                            </div>
                        </div>         
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6" data-aos="fade-right" data-aos-duration="2000">                        
                    <div class="w-100">
                        <h3>KEEP LOGS OF YOUR ACCOMPLISHMENTS</h3>
                        <div class="row">
                            <div class="col-4 x-flex">
                                <img class="bucket-img" src="/images/log.png">
                            </div>
                            <div class="col-8 x-flex">
                                <p>Always wanted to keep track of the books you read or new words you learned? All possible through an easy to use interface.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6" data-aos="fade-left" data-aos-duration="2000">
                    <div class="w-100">
                        <h3>TRACK YOUR PROGRESS</h3>
                        <div class="row">
                            <div class="col-4 x-flex">
                                <img class="bucket-img"  src="/images/chart.png">
                            </div>
                            <div class="col-8 x-flex">
                                <p>Graphs, Averages, Totals. All the information you need to explore how far you've come.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("inc/footer.php"); ?>
    <?php include("inc/externals.php"); ?>

    <script src="/js/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>
</html>