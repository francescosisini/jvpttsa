
<!--
Questo software è composto da una web application in PHP su MySQL. Lo scopo 
è quello di amministtrare i dati raccolti durante le indgini diagnostiche del 
Centro di Malattie Vascolari dell'università di Ferrara.
    Copyright (C) <2017>  <Francesco Sisini>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
-->
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('DICOM/class_dicom.php');
$path=$_GET['path'];
$suid=$_GET['study'];
$project=$_GET['project'];
$mod=$_GET['mod'];
$fname=$path;
$ft=filemtime($fname);
$cvpid=$_GET['cvpid'];

$dt=date("Y-m-d H:i:s",filemtime($fname));

?>
<!DOCTYPE html>
<html>
<body>
<header><small><a href='controller.php?action=start'>Home</a></small></heder><br><br>
<img src='<?php echo $path; ?>' width=400><br><br>
File: <?php echo $path." - ".$dt; ?>
<form action=controller.php?action=insertScreenshot&suid=<?php echo $suid; ?>&cvpid=<?php echo $cvpid; ?> method=POST>
<input type=hidden name=fdate value='<?php echo $dt; ?>'>
<input type=hidden name=fname value='<?php echo $path; ?>'>
<br>Y -0 CVP&nbsp;&nbsp;<input type=text name=pix0 size=3><br><br>
Y 10 CVP&nbsp;<input type=text name=pix10 size=3><br>
<br>
<input type=submit value=ok>
</form>
</body>
</html>
