<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Habit.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Task.php';
$habit_obj = new Habit;
$task_obj = new Task;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {    
    $_POST['error_message'] = "Error: Could not extend habit. Please try again later.";
    header("Location: dashboard.php");
}

$habit = $habit_obj->getHabit($id);

$tasks = $task_obj->getTasksForHabitId($habit['id']);

usort($tasks, function($a, $b) {
    return $a['date'] - $b['date'];
});

$last_task = end($tasks); // grab the most recent task
$missed_day = $last_task['date'] + strtotime("+ 1 day", 0); // increment task by one day to get first missed day
$today = time();
$daysArray = explode(",", $habit['days']); // 'Monday', 'Tuesday', etc
$fill_flag = false;

// while missed day is before today
while ($missed_day < $today) {
    $day = date("l", $missed_day); // day of the week in text

    if (in_array($day,$daysArray)) { // if the missed day is on a day that would've had a task
        $fill_flag = true;
        break;
    }

    $missed_day += strtotime("+ 1 day", 0);
}
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
    .form-check-label {
        font-size: 1rem;
    }

    #compute-group {
        display: none;
    }

    #fill {
        width: 20px;
    }    
    </style>
</head>
<body>
    <?php include("inc/navbar.php");?>
    <div class="container">
        <h1 class="text-center mt-5 display-4">Extend your Habit</h1>
        <p class="text-center">If you'd like to update your habit's details, please do so below!</p>
        <hr class="mb-5">

        <form action="extend_habit_submit.php" method="POST" class="mb-5">
            <div class="form-group mb-4">
                <label><strong>Name</strong></label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $habit['name'] ?>" placeholder="Your habit's name" required/>
            </div>
            <div class="form-group mb-4">
                <label><strong>Description</strong></label>
                <input type="text" class="form-control" id="description" name="description" value="<?php echo $habit['description'] ?>" placeholder="What does your habit require you to do?" required/>
            </div>
            <div class="form-group mb-4">
                <label><strong>Days You'll Complete Your Habit</strong></label>
                <select class="form-control" id="days-multiselect" multiple onChange="updateDaysValue()">
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday" selected="selected">Friday</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                </select>
            </div>
            <div class="form-group mb-4">
                <label><strong>If Your Habit is Measureable, Enter a Unit to Use</strong></label>
                <input type="text" class="form-control" id="unit" name="unit" value="<?php echo $habit['unit'] ?>" placeholder="I.e. miles, pages, minutes" onChange="updateComputeGroup()" optional/>
            </div>
            <div id="compute-group" class="form-group mb-4">
                <label><strong>Do you want the total or average?</strong> (the total miles you've ran or the average number of minutes you spend reading)</label>
                <br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="compute" id="total" value="1">
                    <label class="form-check-label">Total</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="compute" id="average" value="2">
                    <label class="form-check-label">Average</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="compute" id="neither" value="0">
                    <label class="form-check-label">Neither</label>
                </div>
            </div>

            <?php if ($fill_flag) { ?>
            <div class="form-group mb-4">
                <label><strong>Fill In Missed Tasks</strong></label>
                <span>You have some tasks you missed between when your habit ended until now! <br>Would you like to fill in those tasks?</span>
                <input type="checkbox" class="form-control" id="fill" name="fill" optional/>
            </div>
            <input type="hidden" name="last_task_date" value="<?php echo $last_task['date'] ?>"/>
            <?php } ?>

            <input type="hidden" name="user_id" value="<?php echo $user_id ?>"/>
            <input type="hidden" name="id" value="<?php echo $id ?>"/>
            <input type="hidden" name="days" id="days-hidden" value="NULL"/>
            <button type="submit" class="btn btn-primary">Extend Habit</button>
        </form>
    </div>

    <?php include("inc/footer.php"); ?>
    <?php include("inc/externals.php"); ?>
    <link href="css/select2.css" rel="stylesheet" />
    <script src="js/select2.js"></script>
</body>
<script>
    $(document).ready(function() {        
        <?php $days_array = explode(",", $habit['days']); ?>
        $('#days-multiselect').val([<?php echo "'" . implode("','", $days_array) . "'"; ?>]);
        $('#days-multiselect').select2({
            closeOnSelect:  false,
            placeholder: "Which days would you like to complete your habit?",
            minimumResultsForSearch: Infinity
        });


        
        updateDaysValue();
        updateComputeGroup();

        // set the current days the habit is completed

        updateDaysValue();

        // set compute radio value
        switch (<?php echo $habit['compute'] ?>) {
            case 0:
                $('#neither').prop('checked', true);
                break;
            case 1:
                $('#total').prop('checked', true);
                break;
            case 2:
                $('#average').prop('checked', true);
                break;
        }
    });

    /* The multiselect drop down list of days does not allow a value of more than one day 
     * To combat this, a hidden input was created to allow a comma separated list of days
     * This function updates that hidden input (i.e. '1,2' or '2,5,6')
    */
    function updateDaysValue() {
        var days = document.getElementById("days-hidden");
        days.value = $("#days-multiselect").val();
    }

    /* Show the Total/Average radio group only if the user has specified a unit to measure
     * the habit with
    */
    function updateComputeGroup() {
        var compute = document.getElementById("compute-group");
        var unit = document.getElementById("unit").value;

        if (unit) {
            compute.style.display = "block";
        } else {
            compute.style.display = "none";
        }
    }
</script>
</script>
</html>