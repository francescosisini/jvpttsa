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
 <div style="height:45px;width:100%;font-size:22;font-family:verdana;background-color:#000000;"><a href='start.php' style="color:white;">Home</a>
<br><br>

<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("db.php");
$project=$_GET["project"];
?>
Active repository: <b><?php echo $project; ?> </b><br><br>

<?php if($project!='all') echo "<h2>Choose image modality:</h2>";?>
<ul>
<?php
$db = new Db();    
$config = parse_ini_file('./config.ini'); 
$studydir=$config['study_dir'];
if($project!='all'){
	echo "<li><a href='controller.php?action=browse&project=$project&path=$studydir&mod=bmode'>B-mode videoclip</a> <br>";
	echo "<li><a href='controller.php?action=browse&project=$project&path=$studydir&mod=doppler'>Doppler screenshot</a><br>";
	echo "<li><a href='controller.php?action=browse&project=$project&path=$studydir&mod=cvp'>B-mode for CVP</a><br>";
}


$query="SELECT * from us_study";
if($project!="all") $query=$query." where researchID='".$project."'";


$rows = $db -> select($query);
$rn = count($rows,COUNT_NORMAL);
?>
</ul>
<br><br><br>
<hr>
<h2>Existing studies for <?php echo $project; ?>:</h2>
<Table cellspacing="10">
<tr><th></th><th>Date</th><th>Patient ID</th></tr>
<?php
for ($i=0; $i<$rn; $i++) {
	echo "<tr><td><a href='controller.php?action=loadStudy&study=".$rows[$i]['studyInstanceUID']."'>Open</a></td><td>".$rows[$i]['studyDateTime']."</td><td>".$rows[$i]['patientID']."</td></tr>";
}

?>
</table>
 <footer><small>©2017 Francesco Sisini </small></footer>
</body>

</html>
