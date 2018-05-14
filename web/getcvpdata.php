<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'mylibrary.php';
require_once "db.php";

$db = new Db(); 
$sid= $db -> quote($_GET['screenshot']);


$query="SELECT max(pressure) as maxp  FROM `cvp_sampling` WHERE idscreenshot=".$sid;
$rows = $db -> select($query);
$max=$rows[0]['maxp'];
$query="SELECT *, pressure FROM `cvp_sampling` WHERE idscreenshot=".$sid;
$rows = $db -> select($query);
$rn = count($rows,COUNT_NORMAL);
for ($i=0; $i<$rn; $i++) {
	$pr=150-10*getCalibratedCVP($rows[$i]['pressure'],$sid);
	echo $rows[$i]['number'].";".$pr."<br>";
}




?>
