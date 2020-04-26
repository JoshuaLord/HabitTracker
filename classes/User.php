<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/inc/connect.php';

class User {
    function __construct() {
        global $_conn;
        $this->_conn = $_conn;
    }

    public function createUser($first_name, $last_name, $email, $password, $file_id, $five_year) {
        try {
            $sql = "
                INSERT INTO `users` ( 
                    first_name,
                    last_name,
                    email,
                    password,
                    file_id,
                    five_year
                ) VALUES ( 
                    :first_name,
                    :last_name,
                    :email,
                    :password,
                    :file_id,
                    :five_year
                )";
            $stmt = $this->_conn->prepare($sql);
            $values = [
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':email' => $email,
                ':password' => $password,
                ':file_id' => $file_id,
                ':five_year' => $five_year
            ];
            $stmt->execute($values);
            return $this->_conn->lastInsertID();
        } catch (PDOException $e) {
            exit("Failure to insert user.\n" . $e->getMessage());
        }
    }

    public function updateUser($user_id, $first_name, $last_name, $email, $password, $file_id, $five_year) {
        if (empty($user_id)) {
            return NULL;
        }

        if (empty($first_name) && empty($last_name) && empty($email) && empty($password) && empty($file_id) && empty($five_year)) {
            return NULL;
        }

        // array of the values to update
        $a_sql = [];

        if (!empty($first_name)) {
            array_push($a_sql, "first_name = :first_name");
            $values[':first_name'] = $first_name;
        }
        if (!empty($last_name)) {
            array_push($a_sql, "last_name = :last_name");
            $values[':last_name'] = $last_name;
        }
        if (!empty($email)) {
            array_push($a_sql, "email = :email");
            $values[':email'] = $email;
        }
        if (!empty($password)) {
            array_push($a_sql, "password = :password");
            $values[':password'] = $password;
        }
        if (!empty($file_id)) {
            array_push($a_sql, "file_id = :file_id");
            $values[':file_id'] = $file_id;
        }
        if (!empty($five_year)) {
            array_push($a_sql, "five_year = :five_year");
            $values[':five_year'] = $five_year;
        }

        try {
            $sql = "
                UPDATE 
                    `users` 
                SET 
                    " . implode(", ", $a_sql) . "
                WHERE
                    id = :user_id";                
            $stmt = $this->_conn->prepare($sql);
            $values[':user_id'] = $user_id;
            $stmt->execute($values);
            return true;
        } catch (PDOException $e) {
            exit("Failure to update user.\n" . $e->getMessage());
        }
    }

    public function getUser($id) {
        if (empty($id)) {
            return null;
        }

        try {
            $sql = "
                SELECT
                    *
                FROM
                    `users`
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
            exit("Failure to get user from id: " . $id . "\n" . $e->getMessage());
        }
    }

    public function getUserFromEmail($email) {
        if (empty($email)) {
            return null;
        }

        try {
            $sql = "
                SELECT
                    *
                FROM
                    `users`
                WHERE
                    email = :email
                LIMIT 1";
            $stmt = $this->_conn->prepare($sql);
            $values = [
                ':email' => $email
            ];
            $stmt->execute($values);            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit("Failure to get user from email: " . $email . "\n" . $e->getMessage());
        }
    }
}