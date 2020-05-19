<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/User.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Habit.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Task.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/File.php';
$user_obj = new User;
$habit_obj = new Habit;
$task_obj = new Task;
$file_obj = new File;

date_default_timezone_set('America/New_York');

$user_id = $_SESSION['user_id'];
$user = $user_obj->getUser($user_id);
$picture = $file_obj->getFile($user['file_id']);

// grab tasks for today
$start = strtotime(date("Y-m-d 00:00:00"));
$end = strtotime(date("Y-m-d 23:59:59"));

$tasks_today = $task_obj->getTasksForUserId($user_id, $start, $end);

// grab missed tasks from yesterday
$start = strtotime(date("Y-m-d 00:00:00") . "- 1 days");
$end = strtotime(date("Y-m-d 23:59:59") . "- 1 days");

$missed_tasks = $task_obj->getTasksForUserId($user_id, $start, $end, 0);
?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <?php include("inc/css.php"); ?>

    <style>
    .habit_link:hover {
        text-decoration: underline;
    }

    #profile_picture {
        height: 400px;
        border-radius: 5px;
        box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.75);
    }

    #five_years {
        border-radius: 5px;
        box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.75);
        padding: 20px 15px;
    }

    .task-checkbox {
        width: 20px;
        display: inline-block;
    }
    </style>

    <title>Habit Tracker</title>
</head>
<body>
    <?php include("inc/navbar.php");?>

    <div class="container">
        <h1 class="text-center mt-5 display-4">Dashboard - <?php echo date("l F jS, Y") ?></h1>
        <hr class="mb-5">

        <div class="container mt-5 mb-5">
            <div class="row">
                <div class="col-6 d-flex justify-content-center">
                    <img src="data:<?php echo $picture['type'] ?>;base64, <?php echo base64_encode($picture['data']) ?>" id="profile_picture"/>
                </div>
                <div class="col-6" id="five_years">
                    <h2>Myself in Five Years</h2>
                    <hr>
                    <?php echo str_replace("\r\n", "<br><br>", $user['five_year']) ?>
                </div>
            </div>
        </div>

        <hr class="my-3">

        <h2 class="text-center mb-3"><u>Tasks for the Day</u></h2>
        <table class="table table-striped table-hover" id="tasks-table">
            <tr>
                <th>Task</th>
                <th>Log</th>
                <th>Progress</th>
                <th>Completed</th>
            </tr>
            <?php
            $count = 0;
            foreach ($tasks_today as $task) { 
                $habit = $habit_obj->getHabit($task['habit_id']); 
                $formid = "form" . $count++; 
            ?>
            <tr>
                <form id="<?php echo $formid?>" method="POST" action="update_task.php" target="dummyframe"></form>
                <td width=225 class="habit_link" style="cursor: pointer" onclick="document.location = 'view_habit.php?id=<?php echo $habit['id']?>'"><h5><?php echo $habit['name'] ?></h5></td>
                <td width=300 <?php if (empty($habit['unit'])) echo "colspan='2'" ?>>
                    <textarea form="<?php echo $formid?>" type="text" class="form-control" name="log" onChange="this.form.submit()"><?php echo $task['log']; ?></textarea>
                </td>
                <?php if ($habit['unit']) { ?>
                <td width=200>
                    <div class="input-group">
                        <input form="<?php echo $formid?>" type="number" class="form-control text-center" name="progress" 
                                value="<?php if (!empty($task['progress'])) echo $task['progress']; ?>" <?php if (empty($habit['unit'])) echo "disabled"?> onChange="this.form.submit()">
                        <div class="input-group-append">
                            <span class="input-group-text"><?php echo $habit['unit']?></span>
                        </div>
                    </div>
                </td>
                <?php }?>
                <td width=100>
                    <input form="<?php echo $formid?>" type="checkbox" class="form-control task-checkbox" name="complete" <?php if ($task['complete']) echo "checked"; ?> onChange="this.form.submit()"/>
                </td>
                <input form="<?php echo $formid?>" type="hidden" name="task_id" value="<?php echo $task['id']?>">
            </tr>
            <?php } ?>
        </table>

        <?php if (count($missed_tasks) > 0) { ?>
        <h2 class="text-center my-3"><u>Tasks Missed Yesterday</u></h2>
        <table class="table table-striped table-hover" id="tasks-table">
            <tr>
                <th>Task</th>
                <th>Log</th>
                <th>Progress</th>
                <th>Completed</th>
            </tr>
            <?php
            $count = 0;
            foreach ($missed_tasks as $task) { 
                $habit = $habit_obj->getHabit($task['habit_id']); 
                $formid = "form-missed" . $count++; 
            ?>
            <tr>
                <form id="<?php echo $formid?>" method="POST" action="update_task.php" target="dummyframe"></form>
                <td width=225 class="habit_link" style="cursor: pointer" onclick="document.location = 'view_habit.php?id=<?php echo $habit['id']?>'"><h5><?php echo $habit['name'] ?></h5></td>
                <td width=300 <?php if (empty($habit['unit'])) echo "colspan='2'" ?>>
                    <textarea form="<?php echo $formid?>" type="text" class="form-control" name="log" onChange="this.form.submit()"><?php echo $task['log']; ?></textarea>
                </td>
                <?php if ($habit['unit']) { ?>
                <td width=200>
                    <div class="input-group">
                        <input form="<?php echo $formid?>" type="number" class="form-control text-center" name="progress" 
                                value="<?php if (!empty($task['progress'])) echo $task['progress']; ?>" <?php if (empty($habit['unit'])) echo "disabled"?> onChange="this.form.submit()">
                        <div class="input-group-append">
                            <span class="input-group-text"><?php echo $habit['unit']?></span>
                        </div>
                    </div>
                </td>
                <?php }?>
                <td width=100>
                    <input form="<?php echo $formid?>" type="checkbox" class="form-control task-checkbox" name="complete" <?php if ($task['complete']) echo "checked"; ?> onChange="this.form.submit()"/>
                </td>
                <input form="<?php echo $formid?>" type="hidden" name="task_id" value="<?php echo $task['id']?>">
            </tr>
            <?php } ?>
        </table>
        <?php } ?>
        
        <iframe name="dummyframe" id="dummyframe" style="display: none;"></iframe>
    </div>

    <?php include("inc/footer.php"); ?>
    <?php include("inc/externals.php"); ?>
    
    <script>
    $('textarea').each(function () {
        this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
    }).on('input', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    $(document).ready(function () {
        <?php if (!empty($_POST['error_message'])) { ?>
        alert(<?php echo $_POST['error_message']; ?>);
        <?php $_POST['error_message'] = ""; } ?>
    });
    </script>
</body>
</html>