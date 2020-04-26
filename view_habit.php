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
    exit();
}

$habit = $habit_obj->getHabit($id);

// grab tasks in a 7 day range
$start = strtotime(date("Y-m-d") . "- 3 days");
$end = strtotime(date("Y-m-d") . "+ 4 days");

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
    <div class="container">
        <h1 class="mt-5 text-center display-4"><?php echo $habit['name']?></h1>
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

        <h4 class="mb-4">Tasks from <?php echo date('F j', $start); ?> to <?php echo date('F j', $end); ?></h4>
        <table class="table table-striped table-hover mb-5" id="tasks-table">
            <tr>
                <th>Date</th>
                <th>Log (Description of What You Did)</th>
                <?php if ($habit['unit']) { ?>                
                <th>Progress (Number if Applicable)</th>
                <?php } ?>
                <th>Completed</th>
            </tr>
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
                        <input form="<?php echo $formid?>" type="number" class="form-control text-center" id="progress" name="progress" step="0.1" 
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
                    <input form="form-99" type="submit" class="btn btm-sm" value="Create Task">
                </td>
                <input form="form-99" type="hidden" name="habit_id" value="<?php echo $habit['id']?>">
            </tr>
        </table>

        <iframe name="dummyframe" id="dummyframe" style="display: none;"></iframe>
    </div>

    <?php include("inc/externals.php"); ?>
    <?php include("inc/datatables.php"); ?>
    <!-- Draggable Library -->
    <!-- Entire bundle -->
    <script src="https://cdn.jsdelivr.net/npm/@shopify/draggable@1.0.0-beta.8/lib/draggable.bundle.js"></script>
    <!-- legacy bundle for older browsers (IE11) -->
    <script src="https://cdn.jsdelivr.net/npm/@shopify/draggable@1.0.0-beta.8/lib/draggable.bundle.legacy.js"></script>
    <!-- Draggable only -->
    <script src="https://cdn.jsdelivr.net/npm/@shopify/draggable@1.0.0-beta.8/lib/draggable.js"></script>
    <!-- Sortable only -->
    <script src="https://cdn.jsdelivr.net/npm/@shopify/draggable@1.0.0-beta.8/lib/sortable.js"></script>
    <!-- Droppable only -->
    <script src="https://cdn.jsdelivr.net/npm/@shopify/draggable@1.0.0-beta.8/lib/droppable.js"></script>
    <!-- Swappable only -->
    <script src="https://cdn.jsdelivr.net/npm/@shopify/draggable@1.0.0-beta.8/lib/swappable.js"></script>
    <!-- Plugins only -->
    <script src="https://cdn.jsdelivr.net/npm/@shopify/draggable@1.0.0-beta.8/lib/plugins.js"></script>

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