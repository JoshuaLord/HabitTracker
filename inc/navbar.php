<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Habit.php';
$habit_obj = new Habit;

function active($curr_page) {
    $url_array = explode('/', $_SERVER['REQUEST_URI']);
    $url = end($url_array);
    $url = substr($url, 0, strpos($url, "?")); // if any get variables, delete them and the ?
    if ($curr_page == $url) {
        echo 'active';
    }
}

// check to see if any habits are complete
if (!empty($_SESSION['user_id'])) {
    $finishedHabits = $habit_obj->checkHabitFinished($_SESSION['user_id']);
} else {
    $finishedHabits = NULL;
}
?>
<header>
    <!--
    <div id="top-bar">
        <div class="container">
            <nav class="navbar navbar-expand-lg justify-content-end">
                <ul class="navbar-nav">
                    <?php //if (!empty($_SESSION['user_id'])) { ?>
                    <li class="nav-item">
                        <a class="nav-link <?php // echo active("logout.php");?>" href="logout.php">Logout</a>
                    </li>
                    <?php // } ?>
                </ul>
            </nav>
        </div>
    </div>
    -->
    <div id="menu-bar" class="position-absolute">
        <div class="container">
            <nav class="navbar navbar-expand-lg justify-content-space-between">
                <?php if (empty($_SESSION['user_id'])) { ?>
                <a class="nav-link home-link mr-auto" href="index.php">Habit Tracker</a>
                <?php } else { ?>
                <a class="nav-link home-link mr-auto" href="dashboard.php">Habit Tracker</a>
                <?php } ?>
                <ul class="navbar-nav">                    
                    <?php if (!empty($_SESSION['user_id'])) { ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo active("dashboard.php");?>" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo active("habits.php")?>" href="habits.php">Habits</a>
                    </li>
                    <?php if (!empty($finishedHabits)) { ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo active("habit_complete.php")?>" href="habit_complete.php?ids=<?php echo $finishedHabits ?>">Complete</a>
                    </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo active("profile.php")?>" href="profile.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo active("logout.php")?>" href="logout.php">Logout</a>
                    </li>
                    <?php } else { ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo active("login.php")?>" href="login.php">Log In</a>
                    </li>
                    <?php } ?>
                </ul>
            </nav>
        </div>
        
        <iframe name="dummyframe" id="dummyframe" style="display: none;"></iframe>
    </div>
</header>