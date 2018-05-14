<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'mylibrary.php';
require_once "db.php";
$mode=$_GET['mode'];
$db = new Db(); 
//var_dump($db);
$vid= $db -> quote($_GET['video']);

if($mode=="raw"){
	$query="select number*effectiveDuration/numberOfFrames as time,round(csa*phdx*phsx,2) as CSA from sonogram inner join us_videoclip on sonogram.videoclipid=us_videoclip.videoclipid where sonogram.videoclipid=".$vid;
	plotme($query,$db);
}else if($mode=="img")

{
	$query="select max(csa*phdx*phsx) as CSAmax,min(csa*phdx*phsx) as CSAmin  from sonogram inner join us_videoclip on sonogram.videoclipid=us_videoclip.videoclipid where sonogram.videoclipid=".$vid;
	$rows = $db -> select($query);
	$rn = count($rows,COUNT_NORMAL);
	if($rn>0)
	{
		$max= $rows[0]['CSAmax'];
		$min= $rows[0]['CSAmin'];
	}
	$query="select number time,(100-((csa*phdx*phsx-$min)/($max-$min)*100)  ) as CSA from sonogram inner join us_videoclip on sonogram.videoclipid=us_videoclip.videoclipid where sonogram.videoclipid=".$vid;
	plotme($query,$db);
}
else if($mode=="ecgraw")
{
	$rows=getECGplottableData($vid,$waves="PT");
	$rn = count($rows,COUNT_NORMAL);
	for ($i=0; $i<$rn; $i++) {
		echo $rows[$i]['n'].";".$rows[$i]['w']."<br>";
	}
}

function plotme($query,$mdb){
	//var_dump($mdb);
	$rows = $mdb -> select($query);
	$rn = count($rows,COUNT_NORMAL);
	for ($i=0; $i<$rn; $i++) {
		echo $rows[$i]['time'].";".$rows[$i]['CSA']."<br>";
	}
}



?>
