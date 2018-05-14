<html>
<body>
<?php
$type=$_GET['type'];
$video=$_GET['video'];
$SUID=$_GET['study'];
$did=$_GET['doppler'];
$idscreenshot=$_GET['idscreenshot'];
if($type=='csa'){
?>
<header><small><b style="color:green;background-color:pink;">IJV</b><b style="color:white;background-color:pink;">Database</b><b style="color:red;background-color:pink;">System</b>&nbsp; <a href='index.php'>Home</a><a href='controller.php?action=loadStudy&study=<?php echo $SUID;?>'>Back</a></small></heder><br><br>

<center><b>Load CSA data</b></center>
Data format: &lt;sonogram number&gt;;&lt;csa in pixel&gt;;&lt;perimeter in pixel&gt;<br>
<b>Example:</b><br>
1;1200;23<br>
2;1205;23<br>
3;1208;28<br>

<?php
}

if($type=='ecg'){
?>
<b>Load ECG data</b>
Data format: &lt;sonogram number&gt;;&lt;ECG wave&gt;<br>
<b>Example:</b><br>
1;P<br>
5;R<br>
20;T<br>
15;P<br>

<?php
}
?>
<?php


if($type=='jvp'){
?>
<b>Load JVP wave data</b>
Data format: &lt;sonogram number&gt;;&lt;JVP wave&gt;<br>
<b>Example:</b><br>
1;a<br>
5;c<br>
20;x<br>
25;v<br>
28;y<br>
<?php
}

if($type=='doppler'){
?>
<b>Load Doppler sample data</b>
Data format: &lt;x coordinate&gt;;&lt;y coordinate&gt;<br>
<b>Example:</b><br>
1;2<br>
5;20<br>
20;12<br>
25;33<br>
28;12<br>
<?php
}

if($type=='dopplerECG'){
?>
<b>Load Doppler ECG data</b>
Data format: &lt;x coordinate&gt;;&lt;ECG wave&gt;<br>
<b>Example:</b><br>
1;P<br>
5;R<br>
20;T<br>
<?php
}

?>




<form name=rows action='controller.php?action=insertrows&type=<?php echo $type; ?>&video=<?php echo $video; ?>&study=<?php echo $SUID; ?>&doppler=<?php echo $did; ?>&idscreenshot=<?php echo $idscreenshot; ?>' method=POST>
<textarea rows="18" cols="20" name=data>

</textarea><br><br>
<input type=hidden name=video value=<?php echo $video; ?>>
<input type=hidden name=doppler value=<?php echo $did; ?>>
<input type=submit value=Load>
</form>
</body>
</html>
