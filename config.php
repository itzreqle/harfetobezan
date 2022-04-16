<?php
define('Host_Location', '127.0.0.1');
define('Host_Database', 'harfetobezan');
define('Database_Username', 'root');
define('Database_Password', '');


$con = new mysqli(Host_Location, Database_Username, Database_Password, Host_Database);
$con->set_charset("utf8");


include_once('classes.php');
$functions = new functions();


ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
error_reporting(~E_ALL);
