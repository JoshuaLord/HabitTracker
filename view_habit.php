<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Habit.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Task.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Chart.php';
$habit_obj = new Habit;
$task_obj = new Task;
$chart_obj = new Chart;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    header("Location: habits.php");
}

$habit = $habit_obj->getHabit($id);

// grab tasks in a 7 day range
if (isset($_GET['move'])) {
    // make sure there is at least one task in the week the user is grabbing
    $tasksPrev = $task_obj->getTasksForHabitId($id, NULL, strtotime(date("Y-m-d", $_SESSION['end_date']) . "- 7 days"));
    $tasksForw = $task_obj->getTasksForHabitId($id, strtotime(date("Y-m-d", $_SESSION['start_date']) . "+ 7 days"), NULL);

    if ($_GET['move'] == -1 && count($tasksPrev) > 0) { // prev one week
        $start =  strtotime(date("Y-m-d", $_SESSION['start_date']) . "- 7 days");
        $end = strtotime(date("Y-m-d", $_SESSION['end_date']) . "- 7 days");
    } else if ($_GET['move'] == 1 && count($tasksForw) > 0) { // forward one week
        $start =  strtotime(date("Y-m-d", $_SESSION['start_date']) . "+ 7 days");
        $end = strtotime(date("Y-m-d", $_SESSION['end_date']) . "+ 7 days");
    } else {
        $start = $_SESSION['start_date'];
        $end = $_SESSION['end_date'];
    }
} else { // equal today
    $start = strtotime(date("Y-m-d") . "- 3 days");
    $end = strtotime(date("Y-m-d") . "+ 3 days");
}

$_SESSION['start_date'] = $start;
$_SESSION['end_date'] = $end;

$tasks = $task_obj->getTasksForHabitId($id, $start, $end); // tasks for the habit
usort($tasks, function ($a, $b) {
    return $a['date'] - $b['date'];
});

// grab charts
$charts = $chart_obj->getChartsFromHabit($habit['id']);
?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <?php include("inc/css.php"); ?>

    <!-- Chart.css Library -->
    <link rel="stylesheet" link="css/Chart.css">
    <link rel="stylesheet" link="css/Chart.min.css">
    
    <title>Habit Tracker</title>

    <style>
    .task-checkbox {
        height: 20px
    }

    .chart {
        display: inline-block;
        width: 400px;
        height: 400px;
    }
    </style>
</head>
<body>
    <?php include("inc/navbar.php");?>

    <!-- Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header x-flex">
                    <h4 class="modal-title" id="deleteModalLabel">Delete Habit: <u><?php echo $habit['name'] ?></u></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete your habit? This would include deleting all data associated with your habit and tasks.</p>
                    <p>If you are sure, click the delete button.</p>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <a href="delete_habit.php?habit_id=<?php echo $habit['id'] ?>" class="btn btn-red">Delete Habit</a>
                    <button type="button" class="btn" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <div class="container">
        <div class="mt-5 row">
            <div class="col-2"></div>
            <h1 class="col-8 text-center display-4"><?php echo $habit['name']?></h1>
            <h3 class="col-2 d-flex justify-content-end align-items-end">Ends on <?php echo date("M d", $habit['end_date']) ?></h3>
        </div>
        <p class="text-center"><?php echo $habit['description']?></p>
        <p class="days"><?php echo str_replace(",", ", ", $habit['days']); ?></p>
        <hr class="mb-3">

        <div class="d-flex justify-content-around mb-3">
            <?php foreach ($charts as $index => $chart) { ?>
            <div class="chart"><?php echo $chart_obj->getCanvas($chart, $index) ?></div>
            <?php } ?>  
        </div>
        <hr class="mb-3">

        <h4>Days I Complete My Habit</h4>
        <p><?php echo str_replace(",", ", ", $habit['days']); ?></p>

        <h2 class="text-center">Tasks from <?php echo date("M d", $start) ?> to <?php echo date("M d", $end) ?></h2>
        <div class="row my-3">
            <div class="col-4">
                <a class="btn" href="/view_habit.php?move=-1&id=<?php echo $id ?>"><</a>
            </div>
            <div class="col-4 text-center">
                <a class="btn" href="/view_habit.php?id=<?php echo $id ?>">Today</a>
            </div>
            <div class="col-4">
                <a class="btn float-right" href="/view_habit.php?move=1&id=<?php echo $id ?>">></a>
            </div>
        </div>
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
            <?php foreach ($tasks as $task) { ?>
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

        <h4 class="mb-4">Log a Task</h4>
        <table class="table table-striped table-hover" id="tasks-table">
            <tr>
                <th>Date</th>
                <th>Log (Description of What You Did)</th>
                <th>Progress</th>
                <th>Completed</th>
                <th></th>
            </tr>
            <tr>
                <td width=250>
                    <form id="form-99" method="POST" action="create_task_submit.php">
                        <input type="date" class="form-control" name="date" value="<?php echo date("Y-m-d")?>">
                    </form>
                </td>
                <td width=350>
                    <textarea form="form-99" type="text" class="form-control" name="log" value=""></textarea>
                </td>
                <td>
                    <div class="input-group">
                        <input form="form-99" type="number" class="form-control text-center" step="0.1" id="progress" name="progress" value="">
                        <div class="input-group-append">
                            <span class="input-group-text"><?php echo $habit['unit']?></span>
                        </div>
                    </div>
                </td>
                <td width=100>
                    <input form="form-99" type="checkbox" class="form-control task-checkbox" name="complete"/>
                </td>
                <td>
                    <input form="form-99" type="submit" class="btn btm-sm" value="Add Task">
                </td>
                <input form="form-99" type="hidden" name="habit_id" value="<?php echo $habit['id']?>">
            </tr>
        </table>

        <hr class="my-5">
        <div class="x-flex">
            <button type="button" class="btn btn-red" data-toggle="modal" data-target="#deleteModal">Delete Habit</button>
        </div>

        <iframe name="dummyframe" id="dummyframe" style="display: none;"></iframe>
    </div>

    <?php include("inc/externals.php"); ?>
    <?php include("inc/datatables.php"); ?>

    <!-- Chart.js Library -->
    <script src="js/Chart.bundle.js"></script>
    <script src="js/Chart.bundle.min.js"></script>
       
    <script>
    $('textarea').each(function () {
        this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
    }).on('input', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    <?php foreach ($charts as $index => $chart) {    
    if ($chart['y_axis'] == 0) { // completed
        $data = $task_obj->getCompleteForChart($chart['habit_id'], $habit['create_date'], time());
    } else {
        $data = $task_obj->getProgressForChart($chart['habit_id'], $habit['create_date'], time());
    }
    echo $chart_obj->getScript($chart, $index, $data['compute'], $data['date']);
    } ?>
    </script>
</body>
</html>