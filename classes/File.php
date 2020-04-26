<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/inc/connect.php';

class File {
    function __construct() {
        global $_conn;
        $this->_conn = $_conn;
    }

    public function getFile($id) {
        if (empty($id)) {
            return NULL;
        }

        try {
            $sql = "
                SELECT 
                    *
                FROM 
                    `files`
                WHERE
                    id = :id
                LIMIT 1";
            $stmt = $this->_conn->prepare($sql);
            $values = [
                'id' => $id
            ];
            $stmt->execute($values);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit("Failure to get file id: " . $id . "\n" . $e->getMessage());
        }
    }

    public function uploadFile($file) {
        if (empty($file)) {
            return NULL;
        }

        if (empty($_FILES[$file])) {
            return NULL;
        }

        $img_type = $_FILES[$file]['type'];
        $img_data = file_get_contents($_FILES[$file]['tmp_name']);

        try {
            $sql = "
                INSERT INTO `files` (
                    type,
                    data
                ) VALUES (
                    :type,
                    :data
                )";
            $stmt = $this->_conn->prepare($sql);
            $values = [
                ':type' => $img_type,
                ':data' => $img_data
            ];
            $stmt->execute($values);
            return $this->_conn->lastInsertId();
        } catch (PDOException $e) {
            exit("Failure to upload file.\n" . $e->getMessage());
        }
    }

    /* Deletes an existing file
     * PARAMS
     * $id - id of the file to delete
    */
    public function deleteFile($id) {
        if (empty($id)) {
            return NULL;
        }
            
        try {
            $sql = "
                DELETE FROM
                    `files`
                WHERE
                    id = :id";
            $stmt = $this->_conn->prepare($sql);
            $values = [
                ':id' => $id
            ];
            $stmt->execute($values);
        } catch (PDOException $e) {
            exit("Failure to delete file id: " . $id . "\n" . $e->getMessage());
        }
    }
}

?>