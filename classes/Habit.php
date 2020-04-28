<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/inc/connect.php';

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
            exit("Failure to get habit from habit id: " . $id . "\n" . $e->getMessage());
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
            exit("Failure to get habits from habit id: " . $ids . "\n" . $e->getMessage());
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
            exit("Failure to get habits for user_id: " . $user_id . "\n" . $e->getMessage());
        }
    }

    public function createHabit($name, $description, $days, $unit, $compute, $create_date, $user_id) {
        try {
            $sql = "
                INSERT INTO `habits` (
                    name,
                    description,
                    days,
                    unit,
                    compute,
                    create_date,
                    complete,
                    user_id
                ) VALUES (
                    :name,
                    :description,
                    :days,
                    :unit,
                    :compute,
                    :create_date,
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
                ':user_id'      => $user_id
            ];
            $stmt->execute($values);
            return $this->_conn->lastInsertId();
        } catch (PDOException $e) {
            exit("Failure to insert a habit.\n" . $e->getMessage());
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
            exit("Failure to get habits finished user_id: " . $user_id . "\n" . $e->getMessage());
        }

        $finished = [];

        foreach ($finishedHabits as $habit) {
            array_push($finished, $habit['id']);
        }

        return implode(',', $finished);
    }
}
?>