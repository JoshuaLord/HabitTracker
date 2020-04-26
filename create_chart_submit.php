<?php 

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Chart.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Habit.php';
$chart_obj = new Chart;
$habit_obj = new Habit;

date_default_timezone_set('America/New_York');

$habit_id = isset($_POST['habit_id']) ? $_POST['habit_id'] : NULL;
$habit = $habit_obj->getHabit($habit_id);

$start_date = $habit['create_date'];
$end_date = time();

$type = isset($_POST['type']) ? $_POST['type'] : 0; // default line
$frequency = isset($_POST['frequency']) ? $_POST['frequency'] : 0; // default daily
$compute = isset($_POST['compute']) ? $_POST['compute'] : 0; // default none
$y_axis = isset($_POST['y_axis']) ? $_POST['y_axis'] : 0; // default completed unit

$chart_id = $chart_obj->createChart($type, $x_axis, $frequency, $compute, $start_date, $end_date, $y_axis, $habit_id);

header("Location: view_habit.php?id=" . $habit_id);

?>