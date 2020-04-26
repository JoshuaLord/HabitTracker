<?php
date_default_timezone_set('America/New_York');

$db_user="habittracker_db"; // db user
$db_password="afj25j/jf830apqoixmix";// db password (mention your db password here)
$db_database="habittracker";// database name
$db_host="127.0.0.1"; // db server
$db_charset="utf8";

try {
    $_conn = new PDO("mysql:host=$db_host;dbname=$db_database;charset=$db_charset", $db_user, $db_password);
    $_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $_conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    echo $e;
    exit();
}

function pdo_sql_debug($sql,$placeholders){
    foreach($placeholders as $k => $v){
        $sql = preg_replace('/'.$k.'/',"'".$v."'",$sql);
    }
    return $sql;
}
