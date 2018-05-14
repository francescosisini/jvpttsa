<!DOCTYPE html>


<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("db.php");
$db = new Db(); 
$suid= $db -> quote($_GET['study']);
$query="SELECT * from  us_study where studyInstanceUID =".$suid;
$rows = $db -> select($query);
$studyDateTime=$rows[0]['studyDateTime'];
$patientID=$rows[0]['patientID'];
$patientName=$rows[0]['patientName'];

$s=$_GET['study'];
$reported=true;
$query="SELECT * from us_report inner join us_study on us_report.studyInstanceUID=us_report.studyInstanceUID where us_report.studyInstanceUID =".$suid;
$rows = $db -> select($query);
$rn = count($rows,COUNT_NORMAL);
if($rn==0){
	$reported=false;
	$query="SELECT `reportID`, `studyInstanceUID`, 'storia' as `storia`, 'quesito' as `quesito`, 0 as `cca_csa_d`, 0 as `ica_csa_d`,0 as `eca_csa_d`, 0 as `av_csa_d`,0 as `cca_v_d`, 0 as `ica_v_d`,0 as `eca_v_d`, 0 as `av_v_d`, 0 as `j1_csa_d`, 0 as `j2_csa_d`, 0 as `j3_csa_d`,0 as `j1_v_d`,0 as `j2_v_d`, 0 as `j3_v_d`, 0 as `j1_bloccoFlusso_d`,0 as `j1_flussoBi_d`, 0 as `j1_valvolaIpoMobile_d`,0 as `j1_compressioni_d`,0 as `j2_bloccoFlusso_d`,0 as `j2_flussoBi_d`,0 as `j2_valvolaIpoMobile_d`,0 as `j2_compressioni_d`,0 as `j3_bloccoFlusso_d`,0 as `j3_flussoBi_d`,0 as `j3_valvolaIpoMobile_d`,0 as `j3_compressioni_d`, 0 as `cca_csa_s`, 0 as `ica_csa_s`,0 as `eca_csa_s`, 0 as `av_csa_s`,0 as `cca_v_s`, 0 as `ica_v_s`,0 as `eca_v_s`, 0 as `av_v_s`, 0 as `j1_csa_s`, 0 as `j2_csa_s`, 0 as `j3_csa_s`,0 as `j1_v_s`,0 as `j2_v_s`, 0 as `j3_v_s`, 0 as `j1_bloccoFlusso_s`,0 as `j1_flussoBi_s`, 0 as `j1_valvolaIpoMobile_s`,0 as `j1_compressioni_s`,0 as `j2_bloccoFlusso_s`,0 as `j2_flussoBi_s`,0 as `j2_valvolaIpoMobile_s`,0 as `j2_compressioni_s`,0 as `j3_bloccoFlusso_s`,0 as `j3_flussoBi_s`,0 as `j3_valvolaIpoMobile_s`,0 as `j3_compressioni_s` from us_report";
	$rows = $db -> select($query);
	$rn = count($rows,COUNT_NORMAL);
}

function chk($tag){
	if($tag==1) return "checked";
	return "unchecked";
}


?>
<html>
<body>

<header><small><b style="color:green;background-color:pink;">IJV</b><b style="color:white;background-color:pink;">Database</b><b style="color:red;background-color:pink;">System</b>&nbsp; <a href='index.php'>Home</a>&nbsp;<a href='controller.php?action=loadStudy&study=<?php echo $s;?>'> back</a></small></heder><br><br>
<b>Study data:</b><br><br>
<b><li>Study UID</b> <?php echo $suid;?><br> 
<b><li>Study date</b> <?php echo $studyDateTime;?><br>
<b><li>Patient ID</b> <?php echo $patientID;?><br>
<b><li>Patient Name</b> <?php echo $patientName;?><br>

<div><h4>General comments</h4></div>
<form action="controller.php?action=savereport" method="get" id=report >
<input type=hidden name=action value=savereport>
<input type=hidden name=study value=<?php print($s);?>>
<table>
<!-- storia -->
<tr valign=top>
<td>Patient history</td><td><textarea rows="4" cols="50" name=storia id=report>
<?php print($rows[0]['storia']); ?>
</textarea>
</tr>
<!-- quesito -->
<tr valign=top>
<td>Medical question</td><td><textarea rows="4" cols="50" name=quesito id=report>
<?php print($rows[0]['quesito']); ?>
</textarea>
</tr>
</table>
</div>
<div><h4>Inflow</h4></div>
<div><h5>Right side</h5></div>
<table>
<tr valign=top>
<td>CCA</td>
<td>CSA <input type=text name=cca_csa_d value='<?php print($rows[0]['cca_csa_d']); ?>'></td>
<td>Vel. <input type=text name=cca_v_d value='<?php print($rows[0]['cca_v_d']); ?>'
</tr>
<tr valign=top>
<td>ICA</td>
<td>CSA <input type=text name=ica_csa_d value='<?php print($rows[0]['ica_csa_d']); ?>'></td>
<td>Vel. <input type=text name=ica_v_d value='<?php print($rows[0]['ica_v_d']); ?>'
</tr>
<tr valign=top>
<td>ECA</td>
<td>CSA <input type=text name=eca_csa_d value='<?php print($rows[0]['eca_csa_d']); ?>'></td>
<td>Vel. <input type=text name=eca_v_d value='<?php print($rows[0]['eca_v_d']); ?>'
</tr>
<tr valign=top>
<td>AV</td>
<td>CSA <input type=text name=av_csa_d value='<?php print($rows[0]['av_csa_d']); ?>'></td>
<td>Vel. <input type=text name=av_v_d value='<?php print($rows[0]['av_v_d']); ?>'
</tr>
</table>
<div><h5>Left side</h5></div>
<table>
<tr valign=top>
<td>CCA</td>
<td>CSA <input type=text name=cca_csa_s value='<?php print($rows[0]['cca_csa_s']); ?>'></td>
<td>Vel. <input type=text name=cca_v_s value='<?php print($rows[0]['cca_v_s']); ?>'
</tr>
<tr valign=top>
<td>ICA</td>
<td>CSA <input type=text name=ica_csa_s value='<?php print($rows[0]['ica_csa_s']); ?>'></td>
<td>Vel. <input type=text name=ica_v_s value='<?php print($rows[0]['ica_v_s']); ?>'
</tr>
<tr valign=top>
<td>ECA</td>
<td>CSA <input type=text name=eca_csa_s value='<?php print($rows[0]['eca_csa_s']); ?>'></td>
<td>Vel. <input type=text name=eca_v_s value='<?php print($rows[0]['eca_v_s']); ?>'
</tr>
<tr valign=top>
<td>AV</td>
<td>CSA <input type=text name=av_csa_s value='<?php print($rows[0]['av_csa_s']); ?>'></td>
<td>Vel. <input type=text name=av_v_s value='<?php print($rows[0]['av_v_s']); ?>'
</tr>
</table>
<div><h4>Out-flow</h4></div>
<div><h5>Right side</h5></div>
<table>
<tr valign=top>
<td>J1</td>
<td>CSA <input type=text name=j1_csa_d value='<?php print($rows[0]['j1_csa_d']); ?>'></td>
<td>Vel. <input type=text name=j1_v_d value='<?php print($rows[0]['j1_v_d']); ?>'
</tr>
<tr valign=top>
<td>J2</td>
<td>CSA <input type=text name=j2_csa_d value='<?php print($rows[0]['j2_csa_d']); ?>'></td>
<td>Vel. <input type=text name=j2_v_d value='<?php print($rows[0]['j2_v_d']); ?>'
</tr>
<tr valign=top>
<td>J3</td>
<td>CSA <input type=text name=j3_csa_d value='<?php print($rows[0]['j3_csa_d']); ?>'></td>
<td>Vel. <input type=text name=j3_v_d value='<?php print($rows[0]['j3_v_d']); ?>'
</tr>
</table>

<div><h5>Left side</h5></div>
<table>
<tr valign=top>
<td>J1</td>
<td>CSA <input type=text name=j1_csa_s value='<?php print($rows[0]['j1_csa_s']); ?>'></td>
<td>Vel. <input type=text name=j1_v_s value='<?php print($rows[0]['j1_v_s']); ?>'
</tr>
<tr valign=top>
<td>J2</td>
<td>CSA <input type=text name=j2_csa_s value='<?php print($rows[0]['j2_csa_s']); ?>'></td>
<td>Vel. <input type=text name=j2_v_s value='<?php print($rows[0]['j2_v_s']); ?>'
</tr>
<tr valign=top>
<td>J3</td>
<td>CSA <input type=text name=j3_csa_s value='<?php print($rows[0]['j3_csa_s']); ?>'></td>
<td>Vel. <input type=text name=j3_v_s value='<?php print($rows[0]['j3_v_s']); ?>'
</tr>
</table>
<div><h4>CCSVI criteria</h4></div>
<div><h5>Right side</h5></div>
<table>
<tr valign=top>
<td>J1</td>
<td>Block<input type=checkbox name=j1_bloccoFlusso_d <?php print(chk($rows[0]['j1_bloccoFlusso_d'])); ?>></td>
<td>Compression<input type=checkbox name=j1_compressioni_d <?php print(chk($rows[0]['j1_compressioni_d'])); ?>></td>
<td>Valves ipo<input type=checkbox name=j1_valvolaIpoMobile_d <?php print(chk($rows[0]['j1_valvolaIpoMobile_d'])); ?>></td>
<td>Bidirectional flow<input type=checkbox name=j1_flussoBi_d <?php print(chk($rows[0]['j1_flussoBi_d'])); ?>></td>
</tr>

<tr valign=top>
<td>J2</td>
<td>Block<input type=checkbox name=j2_bloccoFlusso_d <?php print(chk($rows[0]['j2_bloccoFlusso_d'])); ?>></td>
<td>Compression<input type=checkbox name=j2_compressioni_d <?php print(chk($rows[0]['j2_compressioni_d'])); ?>></td>
<td>Valves ipo<input type=checkbox name=j2_valvolaIpoMobile_d <?php print(chk($rows[0]['j2_valvolaIpoMobile_d'])); ?>></td>
<td>Bidirectional flow<input type=checkbox name=j2_flussoBi_d <?php print(chk($rows[0]['j2_flussoBi_d'])); ?>></td>
</tr>

<tr valign=top>
<td>J2</td>
<td>Block<input type=checkbox name=j3_bloccoFlusso_d <?php print(chk($rows[0]['j3_bloccoFlusso_d'])); ?>></td>
<td>Compression<input type=checkbox name=j3_compressioni_d <?php print(chk($rows[0]['j3_compressioni_d'])); ?>></td>
<td>Valves ipo<input type=checkbox name=j3_valvolaIpoMobile_d <?php print(chk($rows[0]['j3_valvolaIpoMobile_d'])); ?>></td>
<td>Bidirectional flow<input type=checkbox name=j3_flussoBi_d <?php print(chk($rows[0]['j3_flussoBi_d'])); ?>></td>
</tr>
</table>
<div><h5>Left side</h5></div>
<table>
<tr valign=top>
<td>J1</td>
<td>Block<input type=checkbox name=j1_bloccoFlusso_s <?php print(chk($rows[0]['j1_bloccoFlusso_s'])); ?>></td>
<td>Compression<input type=checkbox name=j1_compressioni_s <?php print(chk($rows[0]['j1_compressioni_s'])); ?>></td>
<td>Valves ipo<input type=checkbox name=j1_valvolaIpoMobile_s <?php print(chk($rows[0]['j1_valvolaIpoMobile_s'])); ?>></td>
<td>Bidirectional flow<input type=checkbox name=j1_flussoBi_s <?php print(chk($rows[0]['j1_flussoBi_s'])); ?>></td>
</tr>

<tr valign=top>
<td>J2</td>
<td>Block<input type=checkbox name=j2_bloccoFlusso_s <?php print(chk($rows[0]['j2_bloccoFlusso_s'])); ?>></td>
<td>Compression<input type=checkbox name=j2_compressioni_s <?php print(chk($rows[0]['j2_compressioni_s'])); ?>></td>
<td>Valves ipo<input type=checkbox name=j2_valvolaIpoMobile_s <?php print(chk($rows[0]['j2_valvolaIpoMobile_s'])); ?>></td>
<td>Bidirectional flow<input type=checkbox name=j2_flussoBi_s <?php print(chk($rows[0]['j2_flussoBi_s'])); ?>></td>
</tr>

<tr valign=top>
<td>J2</td>
<td>Block<input type=checkbox name=j3_bloccoFlusso_s <?php print(chk($rows[0]['j3_bloccoFlusso_s'])); ?>></td>
<td>Compression<input type=checkbox name=j3_compressioni_s <?php print(chk($rows[0]['j3_compressioni_s'])); ?>></td>
<td>Valves ipo<input type=checkbox name=j3_valvolaIpoMobile_s <?php print(chk($rows[0]['j3_valvolaIpoMobile_s'])); ?>></td>
<td>Bidirectional flow<input type=checkbox name=j3_flussoBi_s <?php print(chk($rows[0]['j3_flussoBi_s'])); ?>></td>
</tr>
<tr>
<td align=right colspan=5><br>
<input type=submit name=save value='Save report'>
</td>
<tr>
</table>



</form>
</body>
</html>

