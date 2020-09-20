<?php
$basepath = __DIR__;

require_once $basepath . 'classes/Habit.php';
require_once $basepath . 'classes/Task.php';
$habit_obj = new Habit;
$task_obj = new Task;

$habit_id = isset($_GET['habit_id']) ? $_GET['habit_id'] : NULL;
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : NULL;

if (empty($habit_id) || empty($start_date)) {   
    $_POST['error_message'] = "Error: Please go to the habit page to fill the tasks in.";
    header("Location: dashboard.php");
}

// grab all of the tasks from the start date to today
$missed_tasks = $task_obj->getTasksForHabitId($habit_id, $start_date, time());

$habit = $habit_obj->getHabit($habit_id);

// check to see if any habits are still complete
$finishedHabits = $habit_obj->checkHabitFinished($_SESSION['user_id']);

?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Habit Tracker</title>

    <?php include("inc/css.php"); ?>    

    <style>
    .task-checkbox {
        width: 20px;
        display: inline-block;
    }
    </style>
</head>
<body>
    <?php include("inc/navbar.php");?>

    <div class="container">
        <h1 class="mt-5 text-center display-4">Fill in Missed Tasks</h1>
        <hr class="mb-4">
        <h2 class="mb-5 text-center"><?php echo $habit['name'] ?></h2>


        <table class="table table-striped table-hover mb-5" id="tasks-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Log (Description of What You Did)</th>
                    <?php if ($habit['unit']) { ?>                
                    <th>Progress (Number if Applicable)</th>
                    <?php } ?>
                    <th>Completed</th>
                </tr>
            </thead>
            <tbody>
            <?php $count = 0 ?>
            <?php foreach ($missed_tasks as $task) { ?>
            <?php $formid = "form" . $count++; ?>
                <tr>
                    <td width=250>
                        <form id="<?php echo $formid?>" method="POST" action="update_task.php" target="dummyframe">
                            <?php echo date("l, F jS", $task['date']); ?>
                        </form>
                    </td>
                    <td width=400>
                        <textarea form="<?php echo $formid?>" type="text" class="form-control" name="log" value="" onChange="this.form.submit()"><?php echo $task['log']; ?></textarea>
                    </td>
                    <?php if ($habit['unit']) { ?>
                    <td>
                        <div class="input-group">
                            <input form="<?php echo $formid?>" type="number" class="form-control text-center" name="progress" step="0.1" 
                                value="<?php echo $task['progress']; ?>" onChange="this.form.submit()">
                            <div class="input-group-append">
                                <span class="input-group-text"><?php echo $habit['unit']?></span>
                            </div>
                        </div>
                    </td>
                    <?php } ?>
                    <td width=100>
                        <input form="<?php echo $formid?>" type="checkbox" class="form-control task-checkbox" name="complete" <?php if ($task['complete']) echo "checked"; ?> onChange="this.form.submit()"/>
                    </td>
                    <input form="<?php echo $formid?>" type="hidden" name="task_id" value="<?php echo $task['id']?>">
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <?php if (!empty($finishedHabits)) { ?>
        <div class="row x-flex">
            <h4>Note that you still have completed habits. They can extended or stopped by clicking below or clicking 'Complete' in the menu bar at anytime.</h4>
        </div>
        <?php } ?>
        <div class="row mt-4 mb-5 d-flex justify-content-between">
            <div class="col-4 x-flex">
                <a class="btn" href="view_habit.php?id=<?php echo $habit['id'] ?>">Go To Current Habit</a>
            </div>
            <?php if (!empty($finishedHabits)) { ?>
            <div class="col-4 x-flex">
                <a class="btn" href="habit_complete.php?ids=<?php echo $finishedHabits ?>">Completed Habits</a>
            </div>
            <?php } ?>
            <div class="col-4 x-flex">
                <a class="btn" href="dashboard.php">Go To Dashboard</a>
            </div>
        </div>
    </div>

    <iframe name="dummyframe" id="dummyframe" style="display: none;"></iframe>

    <?php include("inc/footer.php"); ?>
    <?php include("inc/externals.php"); ?>
</body>
</html>