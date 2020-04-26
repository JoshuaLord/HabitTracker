<?php

class Encrypt {
    public function encryptPassword($password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        return $hash;
    }

    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}

?>