<?php
require_once 'db.php';
require_once 'phplot.php';
require_once 'mylibrary.php';

function plotCSAwithECG($csa,$myecg,$min,$delta,$tmax,$max){
	$plot = new PHPlot(800, 200);
	$plot->SetFailureImage(False); // No error images
	$plot->SetPrintImage(False); // No automatic output
	$plot->SetImageBorderType('plain');
	$plot->SetPlotType('lines');
	$plot->SetDataType('data-data');
	$plot->SetDataColors(array('blue'));
	$plot->SetDataValues($csa);
	$plot->SetPlotAreaWorld(0, $min-$delta/8, $tmax, $max);
	$plot->DrawGraph();
	$plot->SetDataColors(array('red'));
	$plot->SetDataValues($myecg);
	$plot->DrawGraph();
	# Main plot title:
	$plot->SetTitle('CSA time diagram (JVP)');
	# Make sure Y axis starts at 0:
	$plot->SetPlotAreaWorld(0, $min-$delta/8, $tmax, $max);
	$plot->DrawGraph();
	return $plot;

}

function plotCSAwithJVP($csa,$myjvp,$min,$delta,$tmax,$max){
	$plot = new PHPlot(800, 200);
	$plot->SetFailureImage(False); // No error images
	$plot->SetPrintImage(False); // No automatic output
	$plot->SetImageBorderType('plain');
	$plot->SetPlotType('lines');
	$plot->SetDataType('data-data');
	$plot->SetDataColors(array('blue'));
	$plot->SetDataValues($csa);
	$plot->SetPlotAreaWorld(0, $min-$delta/8, $tmax, $max);
	$plot->DrawGraph();
	$plot->SetDataColors(array('red'));
	$plot->SetDataValues($myjvp);
	$plot->DrawGraph();
	# Main plot title:
	$plot->SetTitle('CSA time diagram');
	# Make sure Y axis starts at 0:
	$plot->SetPlotAreaWorld(0, $min-$delta/8, $tmax, $max);
	$plot->DrawGraph();
	return $plot;

}

function plotFLow($flow)
{
	$fplot = new PHPlot(800, 200);
	$fplot->SetFailureImage(False); // No error images
	$fplot->SetPrintImage(False); // No automatic output
	$fplot->SetImageBorderType('plain');
	$fplot->SetPlotType('lines');
	$fplot->SetDataType('data-data');
	$fplot->SetDataColors(array('red'));
	$fplot->SetDataValues($flow);
	//$fplot->SetPlotAreaWorld(0, $min-$delta/8, $tmax, $max);
	$fplot->DrawGraph();
	return $fplot;

}

function plotCVP($sid){
//Mostra il Doppler campionato
//$did=getDopplerId($suid,$rl,$jp);
$ddata=getCVPData($sid);
$maxv=max(array_column($ddata,'y'));
$minv=min(array_column($ddata,'y'));
$deltav=$maxv-$minv;
$eddata=getCVPSelectiveECGwaves("*",$sid);
$rn = count($eddata,COUNT_NORMAL);
if($rn==0) return null;

for ($i=0; $i<max(array_column($eddata,"number")); $i++){
			$eddatax[$i]['a']="";
			$eddatax[$i]['x']=$i;
			$eddatax[$i]['y']=0;
}
for ($i=0; $i<$rn; $i++) {
		$ix=$eddata[$i]['number'];
		if($eddata[$i]['PQRSTwave']=='R'){
			$eddatax[$ix]['a']="";
			$eddatax[$ix]['x']=$eddata[$i]['number'];
			$eddatax[$ix]['y']=$maxv;
					}
		if($eddata[$i]['PQRSTwave']=='P'){
			$eddatax[$ix]['a']="";
			$eddatax[$ix]['x']=$eddata[$i]['number'];
			$eddatax[$ix]['y']=$minv+$deltav/2;
		}
		if($eddata[$i]['PQRSTwave']=='T'){
			$eddatax[$ix]['a']="";
			$eddatax[$ix]['x']=$eddata[$i]['number'];
			$eddatax[$ix]['y']=$minv+$deltav/4;
		}
}

$dplot = new PHPlot(800, 300);
$dplot->SetFailureImage(False); // No error images
$dplot->SetPrintImage(False); // No automatic output
$dplot->SetImageBorderType('plain');
$dplot->SetPlotType('lines');
$dplot->SetDataType('data-data');
$dplot->SetDataColors(array('blue'));
$dplot->SetDataValues($ddata);
//$fplot->SetPlotAreaWorld(0, $min-$delta/8, $tmax, $max);
$dplot->DrawGraph();
$dplot->SetDataColors(array('red'));
$dplot->SetDataValues($eddatax);
//$fplot->SetPlotAreaWorld(0, $min-$delta/8, $tmax, $max);
$dplot->DrawGraph();
return $dplot;

}


function plotDoppler($did){
//Mostra il Doppler campionato
//$did=getDopplerId($suid,$rl,$jp);
$ddata=getDopplerData($did);
$maxv=max(array_column($ddata,'y'));
$minv=min(array_column($ddata,'y'));
$deltav=$maxv-$minv;
$eddata=getDopplerSelectiveECGwaves("*",$did);
$rn = count($eddata,COUNT_NORMAL);
if($rn==0) return null;

for ($i=0; $i<max(array_column($eddata,"number")); $i++){
			$eddatax[$i]['a']="";
			$eddatax[$i]['x']=$i;
			$eddatax[$i]['y']=0;
}
for ($i=0; $i<$rn; $i++) {
		$ix=$eddata[$i]['number'];
		if($eddata[$i]['PQRSTwave']=='R'){
			$eddatax[$ix]['a']="";
			$eddatax[$ix]['x']=$eddata[$i]['number'];
			$eddatax[$ix]['y']=$maxv;
					}
		if($eddata[$i]['PQRSTwave']=='P'){
			$eddatax[$ix]['a']="";
			$eddatax[$ix]['x']=$eddata[$i]['number'];
			$eddatax[$ix]['y']=$minv+$deltav/2;
		}
		if($eddata[$i]['PQRSTwave']=='T'){
			$eddatax[$ix]['a']="";
			$eddatax[$ix]['x']=$eddata[$i]['number'];
			$eddatax[$ix]['y']=$minv+$deltav/4;
		}
}

$dplot = new PHPlot(800, 300);
$dplot->SetFailureImage(False); // No error images
$dplot->SetPrintImage(False); // No automatic output
$dplot->SetImageBorderType('plain');
$dplot->SetPlotType('lines');
$dplot->SetDataType('data-data');
$dplot->SetDataColors(array('yellow'));
$dplot->SetDataValues($ddata);
//$fplot->SetPlotAreaWorld(0, $min-$delta/8, $tmax, $max);
$dplot->DrawGraph();
$dplot->SetDataColors(array('red'));
$dplot->SetDataValues($eddatax);
//$fplot->SetPlotAreaWorld(0, $min-$delta/8, $tmax, $max);
$dplot->DrawGraph();
return $dplot;


}


?>
