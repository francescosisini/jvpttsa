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
<div style="height:45px;width:100%;font-size:22;font-family:verdana;background-color:#000000;"><a href='start.php' style="color:white;">Home</a>
<br><br>

<h2>Repository: <?php echo $project; ?></h2>
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
		echo "<img src='img/System-folder-icon.png' width=30px><a href='controller.php?action=$action&study=$suid&cvpid=$cvpid&mod=$mod&project=$project&path=$path/$dirs[$i]'>$dirs[$i]</a><br><hr><br>";
	}else
	{
		echo "<img src='img/test.jpg' width=30px><a href='controller.php?action=$action&study=$suid&cvpid=$cvpid&mod=$mod&project=$project&path=$path/$dirs[$i]'>$dirs[$i]</a><br></a><br><hr><br>";
	}
}
?>
</body>
</html>
