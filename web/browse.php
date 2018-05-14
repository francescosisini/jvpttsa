<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'mylibrary.php';
$path=$_GET['path'];
$project=$_GET['project'];
$mod=$_GET['mod'];
$cvpid=W_GET('cvpid');
$dirs=scandir($path);
$rn = count($dirs,COUNT_NORMAL);
$suid="";
?>
<!DOCTYPE html>
<html>
<body>
<div style="FLOAT:left;height:35px;width:200px;font-size:22;font-family:verdana;background-color:#ffffcc;"><a href='start.php'><img src=img/miniLogo.gif height=34></a></div><div style="FLOAT:left;height:35px;margin-top:0px;background-color:#ccd9ff;width:800px"><a href='listProject.php'>Back</a></div>
<br><br>

<p>Cathegory: <?php echo $project; ?></p>
<?php
if($mod=='screenshot')
{
	$action='addScreenshot';
	$suid=$_GET['study'];
}else
{
	$action='browse';
}

for($i=0;$i<$rn;$i++)
{
	if(is_dir($path.'/'.$dirs[$i]))
	{
		echo "<img src='img/System-folder-icon.png' width=30px><a href='controller.php?action=$action&study=$suid&cvpid=$cvpid&mod=$mod&project=$project&path=$path/$dirs[$i]'>$dirs[$i]</a><br>";
	}else
	{
		echo "<img src='img/test.jpg' width=30px><a href='controller.php?action=$action&study=$suid&cvpid=$cvpid&mod=$mod&project=$project&path=$path/$dirs[$i]'>$dirs[$i]</a><br>";
	}
}
?>
</body>
</html>
