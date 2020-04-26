<?php

?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <?php include("inc/css.php"); ?>    

    <title>Habit Tracker</title>

    <style>
    #login {
        width: 600px;
        height: 800px;
    }
    </style>
</head>
<body>
    <?php include("inc/navbar.php");?>
    <div id="login" class="container">
        <h1 class="mt-5 text-center display-4">Login</h1>
        <hr class="mb-5">
        <form method="POST" action="login_submit.php">
            <div class="form-group">
                <label><strong>Email Address</strong></label>
                <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email address" pattern="[^@\s]+@[^@\s]+\.[^@\s]+" required/>
            </div>
            <div class="form-group">
                <label><strong>Password</strong></label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required/>
            </div>            
            <button type="submit" class="btn">Login!</button>
        </form>
    </div>

    <?php include("inc/footer.php"); ?>
    <?php include("inc/externals.php"); ?>
</body>
</html>