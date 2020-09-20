<?php
$basepath = __DIR__;

require_once $basepath . '../inc/connect.php';

class Habit {
    function __construct() {
        global $_conn;
        $this->_conn = $_conn;
    }

    public function getHabit($id) {
        if (empty($id)) {
            return NULL;
        }

        try {
            $sql = "
                SELECT 
                    *
                FROM
                    `habits`
                WHERE
                    id = :id
                LIMIT 1";
            $stmt = $this->_conn->prepare($sql);
            $values = [
                ':id' => $id
            ];
            $stmt->execute($values);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            debugLog("Habit_errors", "Failure to get habit for id: " . $id, $e, $sql, $values);
            return NULL;
        }
    }

    /* Returns all of the habits from the given str 
     * PARAMS
     * $ids - a string of ids separated by commas
    */
    public function getHabitsFromStr($ids) {
        if (empty($ids)) {
            return NULL;
        }

        try {
            // first explode the ids in an array
            $ids_a = explode(",", $ids);
            // create the string keep track of the id binds
            $ids_s = "";
            $values = [];
            foreach ($ids_a as $id) {
                // add the id bind on the end, put the bind value in the array
                $ids_s .= ":" . $id . ",";
                $values[":" . $id] = $id;
            }
            // remove the last ',' as we don't need it
            $ids_s = substr($ids_s, 0, -1);

            $sql = "
                SELECT 
                    *
                FROM
                    `habits`
                WHERE
                    id IN ( " . $ids_s . " )";
            $stmt = $this->_conn->prepare($sql);      
            $stmt->execute($values);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            debugLog("Habit_errors", "Failure to get habits from string of ids: " . $ids, $e, $sql, $values);
            return NULL;
        }
    }


    public function getHabitsFromUser($user_id) {
        if (empty($user_id)) {
            return NULL;
        }

        try {
            $sql = "
                SELECT 
                    *
                FROM
                    `habits`
                WHERE
                    user_id = :user_id";
            $stmt = $this->_conn->prepare($sql);
            $values = [
                ':user_id' => $user_id
            ];
            $stmt->execute($values);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            debugLog("Habit_errors", "Failure to get habits for user_id: " . $user_id, $e, $sql, $values);
            return NULL;
        }
    }

    public function createHabit($name, $description, $days, $unit, $compute, $create_date, $end_date, $user_id) {
        try {
            $sql = "
                INSERT INTO `habits` (
                    name,
                    description,
                    days,
                    unit,
                    compute,
                    create_date,
                    end_date,
                    complete,
                    user_id
                ) VALUES (
                    :name,
                    :description,
                    :days,
                    :unit,
                    :compute,
                    :create_date,
                    :end_date,
                    0,
                    :user_id
                )";
            $stmt = $this->_conn->prepare($sql);
            $values = [
                ':name'         => $name,
                ':description'  => $description,
                ':days'         => $days,
                ':unit'         => $unit,
                ':compute'      => $compute,
                ':create_date'  => $create_date,
                ':end_date'     => $end_date,
                ':user_id'      => $user_id
            ];
            $stmt->execute($values);
            return $this->_conn->lastInsertId();
        } catch (PDOException $e) {
            debugLog("Habit_errors", "Failure to create habit", $e, $sql, $values);
            return NULL;
        }
    }

    /* This function updates a habit's data */    
    public function extendHabit($habit) {
        if (empty($habit['id'])) {
            return NULL;
        }

        try {
            $sql = "
                UPDATE
                    `habits`
                SET
                    name = :name,
                    description = :description,
                    days = :days,
                    unit = :unit,
                    compute = :compute,
                    end_date = :end_date,
                    complete = 0
                WHERE
                    id = :id";
            $stmt = $this->_conn->prepare($sql);
            $values = [
                ':name'         => $habit['name'],
                ':description'  => $habit['description'],
                ':days'         => $habit['days'],
                ':unit'         => $habit['unit'],
                ':compute'      => $habit['compute'],
                ':end_date'     => $habit['end_date'],
                ':id'           => $habit['id'],
            ];
            $stmt->execute($values);
        } catch (PDOException $e) {
            debugLog("Habit_errors", "Failure to extend habit for id: " . $habit['id'], $e, $sql, $values);
            return NULL;
        }
    }

    public function deleteHabit($id) {
        if (empty($id)) {
            return false;
        }

        try {
            $sql = "
                DELETE FROM 
                    `habits`
                WHERE
                    id = :id;";
            $stmt = $this->_conn->prepare($sql);
            $values = [
                ':id' => $id
            ];
            $stmt->execute($values);
            return true;
        } catch (PDOException $e) {
            debugLog("Habit_errors", "Failure to delete habit for id: " . $id, $e, $sql, $values);
            return false;
        }
    }

    /* Returns true if a user has a finished habit but has not been completed yet */
    public function checkHabitFinished($user_id) {
        if (empty($user_id)) {
            return NULL;
        }

        try {
            $sql = "
                SELECT 
                    *
                FROM
                    `habits`
                WHERE
                    complete = 0 AND
                    end_date < :today AND
                    user_id = :user_id";
            $stmt = $this->_conn->prepare($sql);
            $values = [
                ':today' => time(),
                ':user_id' => $user_id
            ];
            $stmt->execute($values);
            $finishedHabits = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            debugLog("Habit_errors", "Failure to get finished and uncompleted habits for user_id: " . $user_id, $e, $sql, $values);
            return false;
        }

        $finished = [];

        foreach ($finishedHabits as $habit) {
            array_push($finished, $habit['id']);
        }

        return implode(',', $finished);
    }

    public function setComplete($id) {
        if (empty($id)) {
            return false;
        }

        try {
            $sql = "
                UPDATE 
                    `habits`
                SET
                    complete = 1
                WHERE
                    id = :id";
            $stmt = $this->_conn->prepare($sql);
            $values = [
                ':id' => $id
            ];
            $stmt->execute($values);
            return true;
        } catch (PDOException $e) {
            debugLog("Habit_errors", "Failure to set habit complete for id: " . $id, $e, $sql, $values);
            return false;
        }
    }
}
?>