<?php
$basepath = __DIR__;

require_once $basepath . 'classes/Habit.php';
require_once $basepath . 'classes/Task.php';
require_once $basepath . 'classes/Chart.php';
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

$orig_habit = $habit_obj->getHabit($id);

$habit['id'] = $id;
$habit['name'] = isset($_POST['name']) ? $_POST['name'] : $orig_habit['name'];
$habit['description'] = isset($_POST['description']) ? $_POST['description'] : $orig_habit['description'];
$habit['days'] = isset($_POST['days']) ? $_POST['days'] : $orig_habit['days'];
$habit['unit'] = isset($_POST['unit']) ? $_POST['unit'] : $orig_habit['unit'];
$habit['compute'] = isset($_POST['compute']) ? $_POST['compute'] : $orig_habit['compute'];
$habit['end_date'] = strtotime("+ 21 days");

$habit_obj->extendHabit($habit);

// user wants to fill in missed tasks
$fill = isset($_POST['fill']) ? $_POST['fill'] : 0;
debugLog("debug", "Fill: " . $fill);
if ($fill) {
    // grab the last task date 
    $last_task_date = isset($_POST['last_task_date']) ? $_POST['last_task_date'] : $orig_habit['end_date'];
    $missed_date = $last_task_date + strtotime("+ 1 day", 0); // increment task by one day to get first missed day
    
    // create tasks from last task to the end of the extended habit
    $task_obj->createTasks($id, $habit['end_date'], $habit['days'], $missed_date);
    header("Location: fill_tasks.php?habit_id=" . $id . "&start_date=" . $missed_date);
} else {   
    // create tasks from today forward
    $task_obj->createTasks($id, $habit['end_date'], $habit['days']);

    // check to see if any habits are still complete
    $finishedHabits = $habit_obj->checkHabitFinished($_SESSION['user_id']);
    if (!empty($finishedHabits)) {
        header("Location: habit_complete.php?ids=" . $finishedHabits);
    } 
    
    header("Location: view_habit.php?id=" . $id);
}

?>