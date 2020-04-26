<?php 

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/User.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Habit.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Encrypt.php';
$user_obj = new User;
$habit_obj = new Habit;
$encrypt_obj = new Encrypt;

$email = isset($_POST['email']) ? $_POST['email'] : NULL;
$password = isset($_POST['password']) ? $_POST['password'] : NULL;

$user = $user_obj->getUserFromEmail($email);

if (!$encrypt_obj->verifyPassword($password, $user['password'])) {
    exit("Unable to verify password.");
}

$_SESSION['user_id'] = $user['id'];

// check to see if any habits are complete
$finishedHabits = $habit_obj->checkHabitFinished($user['id']);
if (!empty($finishedHabits)) {
    header("Location: habit_complete.php?ids=" . $finishedHabits);
} else {
    header("Location: dashboard.php");
}

?>