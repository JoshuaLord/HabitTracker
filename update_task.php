<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Task.php';
$task_obj = new Task;

if (isset($_POST['task_id'])) {
    $task_id = $_POST['task_id'];

    $log = isset($_POST['log']) ? $_POST['log'] : NULL;
    $progress = isset($_POST['progress']) ? $_POST['progress'] : NULL;
    $complete = isset($_POST['complete']) ? 1 : 0;

    $updated = $task_obj->updateTask($task_id, $log, $progress, $complete);
}

?>