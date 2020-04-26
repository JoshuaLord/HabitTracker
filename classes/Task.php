<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/inc/connect.php';

date_default_timezone_set('America/New_York');

class Task {
    function __construct() {
        global $_conn;
        $this->_conn = $_conn;
    }

    public function getTasksForHabitId($habit_id, $start_date = NULL, $end_date = NULL) {
        if (empty($habit_id)) {
            return NULL;
        }
        
        if (empty($start_date)) {
            $start_date = 0; // the beginning
        }

        if (empty($end_date)) {
            $end_date = 2000000000; // a very long time from now
        }

        try {
            $sql = "
                SELECT
                    *
                FROM
                    `tasks`
                WHERE
                    date >= :start_date AND date <= :end_date AND
                    habit_id = :habit_id";
            $stmt = $this->_conn->prepare($sql);
            $values = [
                ':start_date' => $start_date,
                ':end_date' => $end_date,
                ':habit_id' => $habit_id
            ];
            $stmt->execute($values);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            exit("Failure to get tasks.\n" . $e->getMessage());
        }
    }

    public function getTasksForUserId($user_id, $start_date = NULL, $end_date = NULL) {
        if (empty($user_id)) {
            return NULL;
        }
        
        if (empty($start_date)) {
            $start_date = 0; // the beginning
        }

        if (empty($end_date)) {
            $end_date = 2000000000; // a very long time from now
        }

        try {
            $sql = "
                SELECT
                    t.*
                FROM
                    `tasks` AS t
                INNER JOIN `habits` AS h
                    ON H.id = t.habit_id
                WHERE
                    h.user_id = :user_id AND
                    t.date >= :start_date AND
                    t.date <= :end_date";
            $stmt = $this->_conn->prepare($sql);
            $values = [
                ':user_id' => $user_id,
                ':start_date' => $start_date,
                ':end_date' => $end_date
            ];
            $stmt->execute($values);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);    
        } catch(PDOException $e) {
            exit("Failure to get tasks for user id: " . $user_id . "\n" . $e->getMessage());
        }
    }

    /* Creates and inserts the tasks for a Habit when the habit is created
     * 
     * PARAMS
     * $habit_id    - ID of the habit the tasks are for
     * $end_date    - last date a task should be created
     * $frequency   - daily (0), weekly (1), monthly (1) 
     * $days        - a string of days separated by commas, i.e 'Monday, Wednesday, Thursday'
     * 
     * RETURNS 
     * $inserted    - number of tasks created/inserted for the habit
    */
    public function createTasks($habit_id, $end_date, $days) {
        if (empty($habit_id) || empty($end_date)) {
            exit("Empty parameter in createTasks()");
        }

        $daysArray = explode(",", $days);
        $dates_to_add = [];
        $task_unix = time(); // date of the task in unix time
        $task_date = date("Y-m-d", $task_unix);

        $increment = '+ 1 days';

        // Gather the days to insert
        while ($task_unix < $end_date) { 
            $day = date("l", $task_unix); // day of the week in text
            
            if (in_array($day, $daysArray)) {
                array_push($dates_to_add, $task_date);
            }
            
            $task_unix = strtotime($task_date . $increment);
            $task_date = date("Y-m-d", $task_unix);
        }

        // insert the tasks
        $inserted = 0;

        foreach ($dates_to_add as $date) {
            try {
                $sql = "
                    INSERT INTO `tasks` (
                        complete,
                        date,
                        log,
                        progress,
                        habit_id
                    ) VALUES (
                        0,
                        :date,
                        '',
                        NULL,
                        :habit_id
                    )";
                $stmt = $this->_conn->prepare($sql);
                $values = [
                    ':date' => strtotime($date),
                    ':habit_id' => $habit_id
                ];
                $stmt->execute($values);
                $inserted++;
            } catch (PDOException $e) {
                exit("Failure to insert task for habit id: " . $habit_id . "\n" . $e->getMessage());
            }
        }

        return $inserted;
    }

    /* Creates a task 
     * PARAMS:
     * $date        - the date of the task in unix time
     * $log         - log the user supplies
     * $progress    - progress the user supplies (should be a number)
     * $complete    - if the task is completed or not (0|1)
     * $habit_id    - the respective habit id
    */
    public function createTask($date, $log, $progress, $complete, $habit_id) {
        try {
            $sql = "
                INSERT INTO `tasks` (
                    date,
                    log,
                    progress,
                    complete,
                    habit_id
                ) VALUES (
                    :date,
                    :log,
                    :progress,
                    :complete,
                    :habit_id
                )";
            $stmt = $this->_conn->prepare($sql);
            $values = [
                ':date' => $date,
                ':log' => $log,
                ':progress' => $progress,
                ':complete' => $complete,
                ':habit_id' => $habit_id
            ];
            $stmt->execute($values);
            return $this->_conn->lastInsertId();
        } catch (PDOException $e) {
            exit("Failure to insert task for habit id: " . $habit_id . "\n" . $e->getMessage());
        }
    }

    public function updateTask($task_id, $log, $progress, $complete, $date = NULL) {
        if (empty($task_id)) {
            exit("Empty task id");
        }

        if (!empty($date)) {
            $date_sql = "date = :date,";
            $values[':date'] = $date;
        } else {
            $date_sql = "";
        }

        try {
            $sql = "
                UPDATE
                    `tasks`
                SET
                    " . $date_sql . "
                    complete = :complete,
                    log = :log,
                    progress = :progress
                WHERE
                    id = :task_id";
            $stmt = $this->_conn->prepare($sql);
            $values[':complete'] = $complete;
            $values[':log'] = $log;
            $values[':progress'] = $progress;
            $values[':task_id'] = $task_id;
            $stmt->execute($values);
            return true;
        } catch (PDOException $e) {
            exit("Failure to update task id: " . $task_id . "\n" . $e->getMessage());
        }
    }

    /* Returns a two-dimensional array with the first being the task's progress
     * and the second array being the date of the task
     * Array can be accessed with array['progress'] and array['date']
     * PARAMS:
     * $habit_id - used the determine which tasks to grab
    */
    public function getProgressForChart($habit_id, $start_date, $end_date) {
        if (empty($habit_id)) {
            return NULL;
        }

        $tasks = $this->getTasksForHabitId($habit_id, $start_date, $end_date);

        $progress = [];
        $date = [];

        foreach ($tasks as $task) {
            if (empty($task['progress'])) {
                array_push($progress, 0);
            } else {
                array_push($progress, $task['progress']);
            }
            array_push($date, $task['date']);
        }  

        $array['compute'] = $progress;
        $array['date'] = $date;

        return $array;
    }

    /* Returns a two-dimensional array with the first being the task's complete
     * and the second array being the date of the task
     * Array can be accessed with array['complete'] and array['date']
     * PARAMS:
     * $habit_id - used the determine which tasks to grab
    */
    public function getCompleteForChart($habit_id, $start_date, $end_date) {
        if (empty($habit_id)) {
            return NULL;
        }

        $tasks = $this->getTasksForHabitId($habit_id, $start_date, $end_date);

        $complete = [];
        $date = [];

        foreach ($tasks as $task) {
            if (empty($task['complete'])) {
                array_push($complete, 0);
            } else {
                array_push($complete, $task['complete']);
            }
            array_push($date, $task['date']);
        }  

        $array['compute'] = $complete;
        $array['date'] = $date;

        return $array;
    }

    // Returns the total number of completed tasks for the supplied habit
    public function getCompletedTotal($habit_id) {
        if (empty($habit_id)) {
            return NULL;
        }

        $tasks = $this->getTasksForHabitId($habit_id);

        $sum = 0;

        foreach ($tasks as $task) {
            if (!empty($task['complete'])) {
                $sum++;
            }
        }  

        return $sum;
    }

    /* Returns the total or average of the progress column of completed tasks for the supplied habit
     * PARAMS
     * $habit_id - the habit to grab the tasks for
     * $compute - which method to calculate the return value, 0: none, 1: total, 2: average
    */
    public function getProgressCompute($habit_id, $compute) {
        if (empty($habit_id) || $compute == 0) {
            return NULL;
        }

        $tasks = $this->getTasksForHabitId($habit_id);

        $progress = 0;
        $count = 0;

        foreach ($tasks as $task) {
            if (!empty($task['complete'])) {
                $progress += $task['progress'];
                $count++;
            }
        }  

        if ($compute == 1) {
            return $progress;
        } else if ($compute == 2) {
            if ($count == 0) return 0;
            return number_format($progress / $count, 2);
        } else {
            exit("Not a valid compute value: " . $compute);
        }
    }
}
?>