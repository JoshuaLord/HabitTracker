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
            debugLog("Task_errors", "Failure to get tasks for habit_id: " . $habit_id, $e, $sql, $values);
            return NULL;
        }
    }

    public function getTasksForUserId($user_id, $start_date = NULL, $end_date = NULL, $complete = NULL) {
        if (empty($user_id)) {
            return NULL;
        }
        
        if (is_null($start_date)) {
            $start_date = 0; // the beginning
        }

        if (is_null($end_date)) {
            $end_date = 2000000000; // a very long time from now
        }

        $comp_value = [];
        $comp_str = "";
        if (!is_null($complete)) {
            $comp_value[':complete'] = $complete;
            $comp_str = "AND t.complete = :complete";
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
                    t.date <= :end_date " .
                    $comp_str;
            $stmt = $this->_conn->prepare($sql);
            $values = [
                ':user_id' => $user_id,
                ':start_date' => $start_date,
                ':end_date' => $end_date
            ];
            $values = array_merge($values, $comp_value);
            $stmt->execute($values);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);    
        } catch(PDOException $e) {
            debugLog("Task_errors", "Failure to get tasks for user_id: " . $user_id, $e, $sql, $values);
            return NULL;
        }
    }

    /* Creates tasks for a habit on habit creation
     * 
     * PARAMS
     * $habit_id    - ID of the habit the tasks are for
     * $end_date    - last date a task should be created
     * $days        - a string of days separated by commas, i.e 'Monday, Wednesday, Thursday'
     * 
     * RETURNS 
     * $inserted    - number of tasks created/inserted for the habit
    */
    public function createTasks($habit_id, $end_date, $days, $start_date = NULL) {
        if (empty($habit_id) || empty($end_date)) {
            exit("Empty parameter in createTasks()");
        }

        $daysArray = explode(",", $days);
        $dates_to_add = [];
        $task_unix = !empty($start_date) ? $start_date : time();  // date of the task in unix time
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
                debugLog("Task_errors", "Failure to create tasks for habit_id: " . $habit_id, $e, $sql, $values);
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
            debugLog("Task_errors", "Failure to create task for user_id: " . $user_id, $e, $sql, $values);
            return NULL;
        }
    }

    public function updateTask($id, $log, $progress, $complete, $date = NULL) {
        if (empty($id)) {
            return false;
        }

        if (!empty($date)) {
            $date_sql = "date = :date,";
            $date_value[':date'] = $date;
        } else {
            $date_sql = "";
            $date_value = [];
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
                    id = :id";
            $stmt = $this->_conn->prepare($sql);
            $values = [
                ':complete'     => $complete,
                ':log'          => $log,
                ':progress'     => $progress,
                ':id'           => $id
            ];
            $values = array_merge($date_value, $values);
            $stmt->execute($values);
            return true;
        } catch (PDOException $e) {
            debugLog("Task_errors", "Failure to update task for id: " . $id, $e, $sql, $values);
            return false;
        }
    }

    public function deleteTasksForHabit($habit_id) {
        if (empty($habit_id)) {
            debugLog("Task_errors", "Called with an empty habit id");
            return false;
        }

        try {
            $sql = "
                DELETE FROM 
                    `tasks`
                WHERE
                    habit_id = :habit_id;";
            $stmt = $this->_conn->prepare($sql);
            $values = [
                ':habit_id' => $habit_id
            ];
            $stmt->execute($values);
            return true;
        } catch (PDOException $e) {
            debugLog("Task_errors", "Failure to delete tasks for habit_id: " . $habit_id, $e, $sql, $values);
            return false;
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
            debugLog("Task_errors", "Called with an empty habit id");
            return false;
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
            debugLog("Task_errors", "Called with an empty habit id");
            return false;
        }

        $tasks = $this->getTasksForHabitId($habit_id, $start_date, $end_date);

        $complete = [];
        $date = [];

        foreach ($tasks as $task) {
            array_push($complete, $task['complete']);
            array_push($date, $task['date']);
        }  

        $array['compute'] = $complete;
        $array['date'] = $date;

        return $array;
    }

    /* Returns the total number of complete and not complete tasks for the supplied habit
     * RETURNS: an array with two elements, complete and not_complete 
    */
    public function getStatusTotals($habit_id, $start_date = NULL, $end_date = NULL) {
        if (empty($habit_id)) {
            debugLog("Task_errors", "Called with an empty habit id");
            return false;
        }

        $tasks = $this->getTasksForHabitId($habit_id, $start_date, $end_date);

        $array['complete'] = 0;
        $array['not_complete'] = 0;

        foreach ($tasks as $task) {
            if ($task['complete'] == 0) {
                $array['not_complete']++;
            } else {
                $array['complete']++;
            }
        }  

        return $array;
    }

    /* Returns the total or average of the progress column of completed tasks for the supplied habit
     * PARAMS
     * $habit_id - the habit to grab the tasks for
     * $compute - which method to calculate the return value, 0: none, 1: total, 2: average
    */
    public function getProgressCompute($habit_id, $compute) {
        if (empty($habit_id) || $compute == 0) {
            debugLog("Task_errors", "Called with habit id: {$habit_id} and compute: {$compute}");
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
            debugLog("Task_errors", "Not a valid compute value. Compute: {$compute}");
            return NULL;
        }
    }
}
?>