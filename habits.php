<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/User.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Habit.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Task.php';
$user_obj = new User;
$habit_obj = new Habit;
$task_obj = new Task;

$user_id = $_SESSION['user_id'];

if (empty($user_id)) {
    header("Location: dashboard.php");
}

$user = $user_obj->getUser($user_id);

$habits = $habit_obj->getHabitsFromUser($user['id']);

?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Habit Tracker</title>

    <?php include("inc/css.php"); ?>    

    <style>
    tr:nth-child(3n + 1) {
        background-color: #5680E9;
        cursor: pointer;
    }

    .habit-name {
        color: white;
    }

    tr:hover .habit-name {
        text-decoration: underline;
    }

    .habit-stat {
        color: white;
    }

    .spacer {
        height: 50px;
    }

    .task-row {
        border-bottom: 1px solid #dee2e6;
    }

    .task {
        display: inline-flex;
        flex-direction: column;
        justify-content: space-between;
        vertical-align: text-top;
        width: calc(7.6% - .75rem);
        border-left: 1px solid #F0F0F0;
        border-right: 1px solid #F0F0F0;
    }

    .complete {
        font-weight: bold;
        font-size: 18px;
    }

    .unit {
        font-size: 12px;
        background: #C1C8E4;
        letter-spacing: 1px;
        border-radius: 5px;
        margin: 0 2px;
        padding: 1px 0;
    }

    .day {
        opacity: 0.5;
        font-size: 10px;  
        font-weight: normal;  
    }
    </style>
</head>
<body>
    <?php include("inc/navbar.php");?>

    <div class="container">
        <div class="mt-5 d-flex justify-content-between align-items-center">
            <div></div>
            <h1 class="text-center display-4">Habits</h1>
            <div>
                <a href="create_habit.php" class="btn">Create Habit</a>
            </div>
        </div>
        <hr class="mb-5">
        
        <table class="table">
            <?php foreach ($habits as $habit) { ?>
            <tr onclick="window.location='view_habit.php?id=<?php echo $habit['id']?>'">
                <td>
                    <h4 class="habit-name"><?php echo $habit['name'] ?></h4>
                </td>
                <td class="habit-stat">
                    <?php 
                    if ($habit['compute'] != 0 && !empty($habit['unit'])) {
                        echo $task_obj->getProgressCompute($habit['id'], $habit['compute']) . " " . $habit['unit'];
                        if ($habit['compute'] == 1) echo " total";
                        else echo " on average";
                    } ?>
                </td>
                <td  class="habit-stat">
                    <?php
                    $statuses = $task_obj->getStatusTotals($habit['id']);
                    $task_str = $statuses['complete'] == 1 ? "task" : "tasks";
                    echo $statuses['complete'] ?> completed <?php echo $task_str ?>
                </td>
                <td  class="habit-stat">
                    <?php echo count($task_obj->getTasksForHabitId($habit['id'], time())) ?> tasks left until <?php echo date("M j", $habit['end_date']) ?>
                </td>
            </tr>
            <tr class="task-row">
                <td colspan="4">
                <?php 
                // get the last 14 days of tasks, 13 days plus today
                $start_date = $habit['create_date'] < strtotime("- 14 days") ? strtotime("- 14 days") : $habit['create_date'];
                $unit = !empty($habit['unit']); // if habit has a unit, include unit div
                $tasks = $task_obj->getTasksForHabitId($habit['id'], $start_date, time());

                for ($i = 0; $i < (14 - count($tasks)); $i++) { ?>
                    <div class="task">
                    </div>
                <?php }

                foreach ($tasks as $task) { ?>
                    <div class="task">
                        <div class="day">
                            <?php echo date("n/j", $task['date']) ?>
                        </div>
                        <div class="complete my-1">
                            <?php echo ($task['complete'] == 0) ? "&nbsp;" : "X"; ?>
                        </div>
                        <?php if ($unit) { ?>
                        <div class="unit">
                            <?php
                            echo (!empty($task['progress'])) ? $task['progress'] : ""; 
                            echo "<br>";
                            echo $habit['unit']; 
                            ?>
                        </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                </td>
            </tr>
            <tr class="spacer"></tr>
            <?php } ?>
        </table>
    </div>

    <?php include("inc/footer.php"); ?>
    <?php include("inc/externals.php"); ?>
</body>
</html>