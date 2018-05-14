<?php
session_start();
$_SESSION['name']=NULL;
$_SESSION['FBID']=NULL;
session_unset();
header("Location: index.php");

?>
