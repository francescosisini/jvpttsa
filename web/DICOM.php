

<?PHP
#
# Prints out the DICOM tags in a file specified on the command line
#

/*
wget ftp://dicom.offis.de/pub/dicom/offis/software/dcmtk/dcmtk360/dcmtk-3.6.0.tar.gz 
tar zxvf dcmtk-3.6.0.tar.gz
cd dcmtk-3.6.0
./configure;make;make install

wget --no-check-certificate http://github.com/vedicveko/class_dicom.php/zipball/master
unzip master
mv vedicveko-class_dicom.php* class_dicom_php

*/

require_once('DICOM/class_dicom.php');
$path=$_GET['path'];
$project=$_GET['project'];
$mod=$_GET['mod'];
$fname=$path;
$dirn= dirname($path);
$mc="path=$dirn&mod=$mod&project=$project";
$file = (isset($path) ? $path : '');
$isOK=true; //Posto a false se si trova un parametro DICOM non gestibile 
?>
<html>

 <div style="FLOAT:left;height:35px;width:200px;font-size:22;font-family:verdana;background-color:#ffffcc;"><a href='start.php'><img src=img/miniLogo.gif height=34></a></div><div style="FLOAT:left;height:35px;margin-top:0px;background-color:#ccd9ff;width:800px"><a href='browse.php?<?php echo $mc;?>'>Back</a></div>
<br><br>

<br>
Cathegory: <b><?php echo $project; ?> </b>
<?php

if(!file_exists($fname.".jpg")) {
	$d = new dicom_convert;
	$d->file = $fname;
	$d->dcm_to_jpg();
	$d->dcm_to_tn();
}

if(!$file) {
  print "USAGE: ./get_tags.php <FILE>\n";
  exit;
}

if(!file_exists($file)) {
  print "$file: does not exist\n";
  exit;
}

$d = new dicom_tag($file);
$d->load_tags();

$sdate=$d->get_tag('0008','0020');
$stime=$d->get_tag('0008','0030');
$ctime=$d->get_tag('0008','0033');
$pid=$d->get_tag('0010','0020');
$pname=$d->get_tag('0010','0010');
$eduration=$d->get_tag('0018','0072'); //Total time in seconds that data was actually taken for the entire Multi-frame image.
$pux=$d->get_tag('0018','6024');
$puy=$d->get_tag('0018','6026');
$phdx=$d->get_tag('0018','602c');
$phdy=$d->get_tag('0018','602e');
$SUID=$d->get_tag('0020','000d');
$isn=$d->get_tag('0020','0013');
$nof=$d->get_tag('0028','0008');



if($mod=='bmode')
{
	if($pux!="3" || $puy!="3")
	{
		$isOK=false; //L'unità di misura non è il cm^2.
		$um="<b><font color=red>UNKNOW</font></b>";
	}
	else
	{
		$um="cm<sup>2</sup>";
	}
}else if($mod=='doppler')
{
	if( $puy!="7")
		{
			$isOK=false; //L'unità di misura non è il cm/s.
			$um="<b><font color=red>UNKNOW</font></b>";
		}
		else
		{
			$um="cm/s";
		}	

}

?>

<table valign=top cellspacing=20>
<tr>
<td valign=top >DICOM preview<br><img width=300 src='<?php echo "$fname".".jpg";?>'></td><td valign=top >

<Table>
<tr>
	<td>Study Instance UID</td><td><?php echo "$SUID";?></td>
</tr>
<tr>
	<td>Data</td><td><?php echo $sdate;?> (yyyymmdd)</td>
</tr>
<tr>
	<td>Ora studio</td><td><?php echo $stime;?></td>
</tr>
<tr>
	<td>Ora video</td><td><?php echo $ctime;?> (hhmmss)</td>
</tr>
<tr>
	<td>Patient ID</td><td><?php echo $pid;?></td>
</tr>

<tr>
	<td>Patient Name</td><td><?php echo $pname;?></td>
</tr>
<?php
if($mod=='bmode'){
?>
<tr>
	<td>Video duration</td><td><?php echo $eduration;?> (s)</td>
</tr>
<tr>
	<td>x-UM</td><td><?php echo $pux;?></td>
</tr>

<tr>
	<td>y-UM</td><td><?php echo $puy;?></td>
</tr>
<tr>
	<td>Physic. dx</td><td><?php echo "$phdx ($um)";?></td>
</tr>
<tr>
	<td>Physic. dy</td><td><?php echo "$phdy ($um)";?></td>
</tr>

<tr>
	<td>Video numero</td><td><?php echo "$isn";?></td>
</tr>
<tr>
	<td>Numero di frame</td><td><?php echo "$nof";?></td>
</tr>
<?php
}
if($mod=='doppler'){
?>
<tr>
	<td>Physic. dy</td><td><?php echo "$phdy ($um)";?></td>
</tr>
<?php
}
?>
</table>
</td>
</tr>
<tr>
<td></td>
<td>
<form name='dicom' method=POST action='controller.php?action=insertStudy&mod=<?php echo "$mod";?>'>
<input type=hidden name=action value='insertStudy'>
<input type=hidden name=project value='<?php echo $project;?>' >
<input type=hidden name=sdate value='<?php echo $sdate;?>' >
<input type=hidden name=stime value='<?php echo $stime;?>'>
<input type=hidden name=ctime value='<?php echo $ctime;?>'>
<input type=hidden name=pid value='<?php echo $pid;?>'>
<input type=hidden name=eduration value='<?php echo $eduration;?>'>
<input type=hidden name=pname value='<?php echo $pname;?>'>
<input type=hidden name=pux value='<?php echo $pux;?>'>
<input type=hidden name=puy value='<?php echo $puy;?>'>
<input type=hidden name=phdx value='<?php echo "$phdx";?>'>
<input type=hidden name=phdy value='<?php echo "$phdy";?>'>
<input type=hidden name=SUID value='<?php echo "$SUID";?>'>
<input type=hidden name=isn value='<?php echo "$isn";?>'>
<input type=hidden name=nof value='<?php echo "$nof";?>'>
<input type=hidden name=fname value='<?php echo "$path";?>'>
<b>Please, select:</b><br>
 IJV side: ;&nbsp;&nbsp;&nbsp;<select name=rl>
  <option value='R'>Right</option>
  <option value='L'>Left</option>
</select><br>
 IJV position: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select name=j123>
  <option value='2'>J2</option>
  <option value='1'>J1</option>
<option value='3'>J3</option>
</select> <br>
<?php
if($mod=='doppler'){
?>
Pixel to cm <input type=text  name=ptc><br>
Base line&nbsp;&nbsp;&nbsp;&nbsp;<input type=text  name=baseline><br>

<?php
}
?>
I selected the above parameter <input type=checkbox name=readed>
<?php
if(isOK){
?>
 <input type=submit value="Load study">

<?php
}else{
?>
<input type=button value="PONGO">
<?php
}
?>
</form>
</td>
<tr>
</table>

</body>
</html>



