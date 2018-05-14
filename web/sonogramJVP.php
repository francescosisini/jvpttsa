<?php
# PHPlot Example: Simple line graph
require_once 'phplot.php';
require_once 'mylibrary.php';
require_once 'myPlotlibrary.php';
require_once  "db.php";
$suid=$_GET['study'];
$db = new Db(); 
$vid= $db -> quote($_GET['video']);

//Calcolo Valori limiti degli assi per il plot
$limit=getTraceParameter($vid);
$max=$limit["max"];
$min=$limit["min"];
$avg=$limit["avg"];
$std=$limit["std"];
$delta=$max-$min;
$tmax=$limit["tmax"];
$myjvp=getJVPplottableData($vid);
$myecg=getECGplottableData($vid);
$data=getCalibratedJVP($vid);
$plot=plotCSAwithJVP($data,$myjvp,$min,$delta,$tmax,$max);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
     "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>PHPlot Example - Inline Image</title>
</head>
<body>
<header><small><b style="color:green;background-color:pink;">IJV</b><b style="color:white;background-color:pink;">Database</b><b style="color:red;background-color:pink;">System</b>&nbsp; <a href='controller.php?action=loadStudy&study=<?php echo $suid;?>'>Back</a></small></heder><br><br>
<h1>JVP Plot</h1>
<p>Get data <a href=controller.php?action=getdata&mode=raw&video=<?php echo $_GET['video'];?>>raw</a>&nbsp;<a href=controller.php?action=getdata&mode=img&video=<?php echo $_GET['video'];?>>for ImageJ</a></p>
<img src="<?php echo $plot->EncodeImage();?>" alt="Plot Image">
</body>
</html>

