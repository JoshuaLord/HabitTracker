<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Habit.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Task.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Chart.php';
$habit_obj = new Habit;
$task_obj = new Task;
$chart_obj = new Chart;

if (isset($_POST['id'])) {
    $id = $_POST['id'];
} else {    
    $_POST['error_message'] = "Error: Could not extend habit. Please try again later.";
    header("Location: dashboard.php");
}

date_default_timezone_set('America/New_York');

$habit['id'] = $id;
$habit['name'] = isset($_POST['name']) ? $_POST['name'] : $prev_habit['name'];
$habit['description'] = isset($_POST['description']) ? $_POST['description'] : $prev_habit['description'];
$habit['days'] = isset($_POST['days']) ? $_POST['days'] : $prev_habit['days'];
$habit['unit'] = isset($_POST['unit']) ? $_POST['unit'] : $prev_habit['unit'];
$habit['compute'] = isset($_POST['compute']) ? $_POST['compute'] : $prev_habit['compute'];
$habit['end_date'] = strtotime("+ 21 days");

$habit_obj->extendHabit($habit);

// need to create tasks for the extended time period
$task_obj->createTasks($id, $habit['end_date'], $habit['days']);

header("Location: view_habit.php?id=" . $id);

?>