<?php
require_once 'classes/Habit.php';
require_once 'classes/Task.php';
$habit_obj = new Habit;
$task_obj = new Task;

if (!isset($_GET['ids'])) {
    header("Location: dashboard.php");
}

$habits = $habit_obj->getHabitsFromStr($_GET['ids']);

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
    .habit-row {
        border: 2px solid #212529;
        border-radius: 5px;
        padding: 10px 0;
    }
    </style>
</head>
<body>
    <?php include("inc/navbar.php");?>

    <div class="container">
        <?php $habitStr = count($habits) > 1 ? "some habits" : "a habit"; ?>
        <h1 class="mt-5 text-center display-4">Congratulations! You have completed <?php echo $habitStr ?>!</h2>
        <hr class="mb-5">
        <p>You did it! 21 days of your habit and here you are! Time to look forward and evaluate how you did. Remember that life is growing
        process, we all can't succeed on the first try and that's okay. If you did kick your habit's butt, that's great! Remember your accomplishment
        and take some time to dwell on it. It's hard to make a lifestyle change but what's the most important is giving yourself credit!</p>
        
        <p>Now for evaluation. Whether you did great or you're ready to retry. Habit Tracker allows you to continue your habits. Make changes or keep things
        going how they are, it's up to you! Remember tips for setting good habits and goals.</p>
        <ul>
            <li><strong>Specific</strong> - direct, detailed, decisive. When you have a clear path forward in your mind of how you'll complete your habit each day, it makes it so much easier.</li>
            <li><strong>Measureable</strong> - great habits can be tracked! Try to record a log of how your habit went and make sure to set some progress!</li>
            <li><strong>Attainable</strong> - habits should be just hard enough you have to push yourself a little, but not so hard you hate doing them.</li>
            <li><strong>Relevant</strong> - make sure your habit aligns with the idenity you want. Whether that be a more healthy lifestyle, self improvement, or even more relaxation time at the end of the day. Make sure your habit is relevant to you!</li>
            <li><strong>Time-Oriented</strong> -  don't worry, we've got you covered on this. All habits are set for 21 days allowing you to focus in.</li>
        </ul>
        <p class="mt-4 mb-5">Did Great? Make it a little more challenging. Struggled some days? That's okay, lessen the time you spend or the difficulty of the task.</p>

        <p class="text-center mb-5"><strong>Remember! All habits are set for 21 days!</strong></p>

        <?php foreach ($habits as $habit) { 
        $statuses = $task_obj->getStatusTotals($habit['id']);
        $total = count($task_obj->getTasksForHabitId($habit['id']));
        $task_str = $statuses['complete'] == 1 ? "task" : "tasks";

        ?>
        <div class="habit-row my-5">
            <div class="row my-4 x-flex">
                <div class="col-3 text-center"><h4><?php echo $habit['name'] ?></h4></div>
                <div class="col-3 text-center"><?php echo $habit['description'] ?></div>
                <div class="col-3 text-center"><?php echo $statuses['complete'] . " of " . $total . " " . $task_str ?> completed</div>
                <div class="col-1 text-center"><a class="btn" href="extend_habit.php?id=<?php echo $habit['id'] ?>">Extend!</a></div>
                <div class="col-2 text-center"><a class="btn btn-red" href="stop_habit.php?id=<?php echo $habit['id'] ?>">Stop Habit</a></div>
            </div>
        </div>
        <?php } ?>
    </div>

    <?php include("inc/footer.php"); ?>
    <?php include("inc/externals.php"); ?>
</body>
</html>