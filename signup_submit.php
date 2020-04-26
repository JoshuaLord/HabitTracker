<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/User.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Encrypt.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/File.php';
$user_obj = new User;
$encrypt_obj = new Encrypt;
$file_obj = new File;

$first_name = isset($_POST['first_name']) ? $_POST['first_name'] : NULL;
$last_name = isset($_POST['last_name']) ? $_POST['last_name'] : NULL;
$email = isset($_POST['email']) ? $_POST['email'] : NULL;
$password = isset($_POST['password']) ? $encrypt_obj->encryptPassword($_POST['password']) : NULL;
$file_id = $file_obj->uploadFile('picture');
$five_year = isset($_POST['five_year']) ? $_POST['five_year'] : NULL;

$id = $user_obj->createUser($first_name, $last_name, $email, $password, $file_id, $five_year);

$_SESSION['user_id'] = $id;

header("Location: dashboard.php");

?>