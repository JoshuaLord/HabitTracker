<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '../..');
$dotenv->load();

$db_user = getenv('db_user'); //"habittracker_db"; // db user
$db_password = getenv('db_password'); //"83ajdicnr/akd8pqjdx0";// db password (mention your db password here)
$db_database = getenv('db_database'); //"habittracker";// database name
$db_host = "127.0.0.1"; // db server
$db_charset = "utf8";

date_default_timezone_set('America/New_York');

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
