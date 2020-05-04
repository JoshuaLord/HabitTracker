<?php 

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Habit.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Task.php';
$habit_obj = new Habit;
$task_obj = new Task;

if (isset($_GET['habit_id'])) {
    $habit_id = $_GET['habit_id'];
} else {
    exit("Did not supply habit id.");
}

$task_obj->deleteTasksForHabit($habit_id);

$habit_obj->deleteHabit($habit_id);

header("Location: habits.php");
?>