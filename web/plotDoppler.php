<?php
# PHPlot Example: Simple line graph
require_once 'phplot.php';
require_once 'mylibrary.php';
require_once 'myPlotlibrary.php';
require_once 'db.php';

$db = new Db(); 
$did=$_GET['doppler'];
$suid=$_GET['study'];
$dplot=plotDoppler($did);
?>
<header><small><b style="color:green;background-color:pink;">IJV</b><b style="color:white;background-color:pink;">Database</b><b style="color:red;background-color:pink;">System</b>&nbsp; <a href='controller.php?action=loadStudy&study=<?php echo $suid;?>'>Back</a></small></heder><br><br>
<img src="<?php echo $dplot->EncodeImage();?>" alt="Plot Image">
