<?php
# PHPlot Example: Simple line graph
require_once 'phplot.php';
include("db.php");
$db = new Db(); 
$suid=$_GET['study'];
$vid= $db -> quote($_GET['video']);
$vvd=$_GET['video'];



$query="select  round(max(csa*phdx*phsx),2) as max, round(min(csa*phdx*phsx),6) as min, max(number*effectiveDuration/numberOfFrames) as tmax  from sonogram  inner join us_videoclip on sonogram.videoclipid=us_videoclip.videoclipid where sonogram.videoclipid=".$vid;
$rows = $db -> select($query);
$min=$rows[0]['min'];
$max=$rows[0]['max'];
$tmax=$rows[0]['tmax'];

$query="select '',number*effectiveDuration/numberOfFrames as time,round(csa*phdx*phsx,6) as CSA from sonogram inner join us_videoclip on sonogram.videoclipid=us_videoclip.videoclipid where sonogram.videoclipid=".$vid;

//echo $query;
$rows = $db -> select($query);
$rn = count($rows,COUNT_NORMAL);


$data=$rows;
$plot = new PHPlot(800, 200);
$plot->SetFailureImage(False); // No error images
$plot->SetPrintImage(False); // No automatic output
$plot->SetImageBorderType('plain');
$plot->SetPlotType('lines');
$plot->SetDataType('data-data');
$plot->SetDataValues($data);
# Main plot title:
$plot->SetTitle('Juguale Venous Pulse:'.$rn);
# Make sure Y axis starts at 0:
$plot->SetPlotAreaWorld(0, $min-($min/10.0), $tmax, $max);
$plot->DrawGraph();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
     "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>PHPlot Example - Inline Image</title>
<script>
	function myFunction(){
	var ok=confirm("Eliimare il tracciato dal DB?");
	if(ok){
		location.href="controller.php?action=deleteSonogram&video=<?php echo $vvd; ?>&study=<?php echo $suid; ?>";
	}
}
</script>
</head>
<body>


<div style="FLOAT:left;height:35px;width:200px;font-size:22;font-family:verdana;background-color:#ffffcc;"><a href='start.php'><img src=img/miniLogo.gif height=34></a></div><div style="FLOAT:left;height:35px;margin-top:0px;background-color:#ccd9ff;width:800px"> <a href='controller.php?action=loadStudy&study=<?php echo $suid;?>'>Back</a></div>

<h1>JVP Plot</h1>
<p>Get data <a href=controller.php?action=getdata&mode=raw&video=<?php echo $_GET['video'];?>>raw</a>&nbsp;<a href=controller.php?action=getdata&mode=img&video=<?php echo $_GET['video'];?>>for ImageJ</a></p>
<img src="<?php echo $plot->EncodeImage();?>" alt="Plot Image"><br><br>
<button onclick="myFunction()">Delete from DB</button>

</body>

</html>

