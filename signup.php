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
</head>
<body>
    <?php include("inc/navbar.php");?>
    <div class="container">
        <h2>Sign Up for Habit Tracker</h2>
        <form action="signup_submit.php" method="POST" onsubmit="return validateForm()" enctype="multipart/form-data">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Your first name" required/>
            </div>
            <div class="form-group mb-5">
                <label>Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Your last name" required/>
            </div>
            <div class="form-group mb-5">
                <label>Email Address</label>
                <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email address" pattern="[^@\s]+@[^@\s]+\.[^@\s]+" required/>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Please enter a password at least 8 characters long" minlength="8" required/>
            </div>
            <div class="form-group mb-5">
                <label>Re-Enter Password</label>
                <input type="password" class="form-control" id="re_password" name="re_password" placeholder="Please enter your password again" minlength="8" required/>
            </div>
            <div class="form-group mb-5">
                <label>Yourself in Five Years</label>
                <textarea class="form-control" id="five_year" name="five_year" placeholder="Where do you see yourself in five years?" required></textarea>
            </div>
            <div class="form-group mb-5">
                <label>Picture of Yourself</label>
                <input type="file" class="form-control-file" id="picture" name="picture">
            </div>
            <button type="submit" class="btn">Sign Up!</button>
        </form>
    </div>

    <?php include("inc/footer.php"); ?>
    <?php include("inc/externals.php"); ?>
</body>
<script>
    function validateForm() {
        var password = $('#password').val();
        var re_password = $('#re_password').val();

        if (password.localeCompare(re_password) == 0) {
            return true;
        } else {
            alert("Passwords do not match.");
            return false;
        }
    }
</script>
</html>