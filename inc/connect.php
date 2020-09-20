<?php
$basepath = __DIR__;

require_once $basepath . '../vendor/autoload.php';

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
    debugLog("Connect_errors", "Error connecting to database.", $e);
    exit();
}

function pdo_sql_debug($sql,$placeholders){
    foreach($placeholders as $k => $v){
        $sql = preg_replace('/'.$k.'/',"'".$v."'",$sql);
    }
    return $sql;
}

function generateCallTrace()
{
    $e = new Exception();
    $trace = explode("\n", $e->getTraceAsString());
    // reverse array to make steps line up chronologically
    $trace = array_reverse($trace);
    array_shift($trace); // remove {main}
    array_pop($trace); // remove call to this method
    $length = count($trace);
    $result = array();
   
    for ($i = 0; $i < $length; $i++)
    {
        $result[] = ($i + 1)  . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
    }
   
    return "\t" . implode("\n\t", $result);
}

function debugLog($log_filename, $message, $exception = NULL, $sql = NULL, $values = NULL) {
    $basepath = __DIR__;
    $backtrace = debug_backtrace();
    $caller = array_shift($backtrace);
    $file = substr($caller['file'], $_SERVER['DOCUMENT_ROOT'] + 1); // don't need the starting /
    $line = $caller['line'];    

    $content  = "--------------------------------------------------------------" . "\n";
    $content .= "{$file} on Line #{$line} | " . date("D Y-m-d h:i:s A") . "\n";
    $content .= "--------------------------------------------------------------" . "\n\n";
    $content .= "{$message}" . "\n\n";

    if (!empty($exception)) {
        $content .= "{$exception->getMessage()}" . "\n\n";
    }

    if (!empty($sql)) {
        if (!empty($values)) {
            $sql = pdo_sql_debug($sql, $values);
        }   

        $content .= "{$sql}" . "\n";
    }

    $content .= "\n";

    file_put_contents($basepath . "../logs/" . $log_filename . ".log", $content, FILE_APPEND);
}   