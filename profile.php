<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/User.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/File.php';
$user_obj = new User;
$file_obj = new File;

$user_id = $_SESSION['user_id'];

$user = $user_obj->getUser($user_id);

$file_id = $user['file_id'];

$picture = $file_obj->getFile($file_id);

?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Habit Tracker</title>

    <style>
    #profile_picture {
        height: 400px;
        border-radius: 5px;
        box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.75);
        max-width: 100%;
    }

    #picture_group img,
    #picture_group input {
        width: 100%;
    }
    </style>

    <?php include("inc/css.php"); ?>    
</head>
<body>
    <?php include("inc/navbar.php");?>

    <div class="container">
        <h1 class="mt-5 text-center">Profile</h1>
        <hr class="mb-5">

        <div class="row">
            <div class="col-4">
                <form action="profile_submit.php" method="POST" enctype="multipart/form-data">
                    <img src="data:<?php echo $picture['type'] ?>;base64, <?php echo base64_encode($picture['data']) ?>" id="profile_picture"/>
                    <input type="file" class="form-control-file mt-3" id="picture" name="picture" onChange="this.form.submit()">
                </form>
            </div>    
            <div class="col-8">                
                <form action="profile_submit.php" method="POST" target="dummyframe">
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="mr-3"><strong>First Name:</strong></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $user['first_name'] ?>" onChange="this.form.submit()">
                        </div>
                        <div class="col-6">
                            <label class="mr-3"><strong>Last Name:</strong></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $user['last_name'] ?>" onChange="this.form.submit()">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="mr-3"><strong>Email:</strong></label>
                            <input type="text" class="form-control" id="email" name="email" value="<?php echo $user['email'] ?>" onChange="this.form.submit()">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="mr-3"><strong>Password:</strong></label>
                            <input type="password" class="form-control" id="password" name="password" onChange="this.form.submit()">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="mr-3"><strong>Re-enter Password:</strong></label>
                            <input type="password" class="form-control" id="re_password" name="re_password" onChange="this.form.submit()">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 form-group">
                            <label class="mr-3"><strong>Yourself in Five Years</strong></label>
                            <textarea class="form-control" id="five_year" name="five_year" onChange="this.form.submit()"><?php echo $user['five_year'] ?></textarea>
                        </div>
                    </div>              
                    <input type="hidden" name="user_id" value="<?php echo $user['id']?>">
                </form>
            </div>  
        </div>
    </div>

    <iframe name="dummyframe" id="dummyframe" style="display: none;"></iframe>

    <?php include("inc/footer.php"); ?>
    <?php include("inc/externals.php"); ?>
    <script>
    $('textarea').each(function () {
        this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
    }).on('input', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
    </script>
</body>
</html>