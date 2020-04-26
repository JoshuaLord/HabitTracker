<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Task.php';
$task_obj = new Task;

date_default_timezone_set('America/New_York');

$date = isset($_POST['date']) ? strtotime(date($_POST['date'] . " 00:00:00")) : NULL;
$log = isset($_POST['log']) ? $_POST['log'] : NULL;
$progress = isset($_POST['progress']) ? $_POST['progress'] : NULL;
$complete = isset($_POST['complete']) ? 1 : 0;
$habit_id = isset($_POST['habit_id']) ? $_POST['habit_id'] : NULL;

$task_obj->createTask($date, $log, $progress, $complete, $habit_id);

header("Location: view_habit.php?id=" . $habit_id);
?>