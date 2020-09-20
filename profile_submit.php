<?php

require_once 'classes/User.php';
require_once 'classes/Encrypt.php';
require_once 'classes/File.php';
$user_obj = new User;
$encrypt_obj = new Encrypt;
$file_obj = new File;

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : exit();

$first_name = isset($_POST['first_name']) ? $_POST['first_name'] : NULL;
$last_name = isset($_POST['last_name']) ? $_POST['last_name'] : NULL;
$email = isset($_POST['email']) ? $_POST['email'] : NULL;
$five_year = isset($_POST['five_year']) ? $_POST['five_year'] : NULL;

$user = $user_obj->getUser($user_id);

// handle the password
$password = isset($_POST['password']) ? $encrypt_obj->encryptPassword($_POST['password']) : NULL;

// handle the file
$new_file_id = $file_obj->uploadFile('picture');

if (!empty($new_file_id)) {
    $file_obj->deleteFile($user['file_id']);
    $file_id = $new_file_id;
} else {
    $file_id = NULL;
}

$file_id = empty($new_file_id) ? NULL : $new_file_id;


echo $user_obj->updateUser($user_id, $first_name, $last_name, $email, $password, $file_id, $five_year);

header("Location: profile.php");

?>