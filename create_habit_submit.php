<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Habit.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Task.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Chart.php';
$habit_obj = new Habit;
$task_obj = new Task;
$chart_obj = new Chart;

date_default_timezone_set('America/New_York');

$name = isset($_POST['name']) ? $_POST['name'] : NULL;
$description = isset($_POST['description']) ? $_POST['description'] : NULL;
$days = isset($_POST['days']) ? $_POST['days'] : NULL;
$unit = isset($_POST['unit']) ? strtolower($_POST['unit']) : NULL;
$compute = isset($_POST['compute']) ? $_POST['compute'] : 0;
$create_date = time();
$end_date = strtotime("+ 21 days");
$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : NULL;

$id = $habit_obj->createHabit($name, $description, $days, $unit, $compute, $create_date, $end_date, NULL, $user_id);

// need to create tasks for the habit
$task_obj->createTasks($id, $end_date, $days);

// need to create the chart for the habit
$chart_obj->createChart($id, 0);

if (!empty($unit)) {
    $chart_obj->createChart($id, 1);
}

header("Location: view_habit.php?id=" . $id);

?>