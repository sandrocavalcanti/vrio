<?php 
$host = 'localhost';
$user = 'root';
$pass = 'root';
$dbname = 'vrio';

$link = mysql_connect($host, $user, $pass);
if (!$link) {
    die('Não foi possível conectar: ' . mysql_error());
}
mysql_select_db($dbname);
?>