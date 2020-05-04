<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Habit.php';
$habit_obj = new Habit;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    header("Location: habits.php");
}

if ($habit_obj->setComplete($id)) {
    echo "Updated";
}
echo $id;
//header("Location: dashboard.php");

?>