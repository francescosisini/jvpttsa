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
<!DOCTYPE html>
<html>
<body>
<div style="FLOAT:left;height:35px;width:200px;font-size:22;font-family:verdana;background-color:#ffffcc;"><a href='start.php'><img src=img/miniLogo.gif height=34></a></div><div style="FLOAT:left;height:35px;margin-top:0px;background-color:#ccd9ff;width:800px"><a href="#" onclick="history.go(-1);">back</a></div>


<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("db.php");
$project=$_GET["project"];
?>

<?php
$db = new Db();    
$config = parse_ini_file('./config.ini'); 
$studydir=$config['study_dir'];


$query="SELECT * from us_study order by researchID";
if($project!="all") $query=$query." where researchID='".$project."'";


$rows = $db -> select($query);
$rn = count($rows,COUNT_NORMAL);
?>
<br><br><br>
<hr>

<Table cellspacing="1">
<tr><th></th><th>Date</th><th>Patient ID</th><th>Study type</th></tr>
<?php

$rlc=true;
for ($i=0; $i<$rn; $i++) {
	if($rlc){
		
		$color="#99ccff";
	}else{
		$color="#ffff99";
	}
	$rlc=!$rlc;
	echo "<tr bgcolor=$color ><td><a href='controller.php?action=researchreport&study=".$rows[$i]['studyInstanceUID']."'>Open</a></td><td>".$rows[$i]['studyDateTime']."</td><td>".$rows[$i]['patientID']."</td><td>".$rows[$i]['researchID']."</td></tr><hl>";
}

?>
</table>
 <footer><small>©2017 Francesco Sisini </small></footer>
</body>

</html>
