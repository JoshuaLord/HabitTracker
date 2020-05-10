<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Habit.php';
$habit_obj = new Habit;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    header("Location: habits.php");
}

$habit = $habit_obj->getHabit($id);

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
    </style>
</head>
<body>
    <?php include("inc/navbar.php");?>
    <div class="container">
        <h1 class="text-center mt-5 display-4">Extend your Habit</h1>
        <p class="text-center">If you'd like to update your habit's details, please do so below!</p>
        <hr class="mb-5">

        <form action="extend_habit_submit.php" method="POST">
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