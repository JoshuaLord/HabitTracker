<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Habit.php';
$habit_obj = new Habit;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    $_POST['error_message'] = "Error: Could not stop habit. Please try again later.";
    header("Location: dashboard.php");
}

if (!$habit_obj->setComplete($id)) {
    $_POST['error_message'] = "Error: Could not stop habit. Please try again later.";
}

header("Location: dashboard.php");

?>