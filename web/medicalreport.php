<?php
# PHPlot Example: Simple line graph
require_once 'phplot.php';
include("db.php");
$db = new Db(); 
$suid=$_GET['study'];


$rowStudy = $db -> select("SELECT * from us_videoclip where studyInstanceUID =".$db->quote($suid));
$rn = count($rowStudy,COUNT_NORMAL);
if($rn==0) die("...Problem...".$query);
$vid= $rowStudy[0]['videoclipID'];

//Calcolo Valori limiti degli assi per il plot
$query="select  round(max(csa*phdx*phsx),2) as max, round(min(csa*phdx*phsx),2) as min, max(number*effectiveDuration/numberOfFrames) as tmax  from sonogram  inner join us_videoclip on sonogram.videoclipid=us_videoclip.videoclipid where sonogram.videoclipid=".$vid;
$rows = $db -> select($query);
$min=$rows[0]['min'];
$max=$rows[0]['max'];
$delta=$max-$min;
$tmax=$rows[0]['tmax'];


//Fattore di conversione pixel->cm^2 e frame->s
$calibration=0;
$query="select  phdx*phsx as calibration, effectiveDuration/numberOfFrames as tc  from  us_videoclip  where videoclipid=".$vid;
$rows = $db -> select($query);
$rn = count($rows,COUNT_NORMAL);
if($rn==0) die("...Problem...".$query);
$calibration=$rows[0]['calibration'];
$tc=$rows[0]['tc'];

//Calcolo media e std Wave 'a'
$query="SELECT avg(CSA) as m, std(csa) as d from sonogram inner join us_jvp on (sonogram.number=us_jvp.number and sonogram.videoclipid=us_jvp.videoclipid) WHERE (us_jvp.acxvyWave='a' and sonogram.videoclipid=".$vid.")";
$rows = $db -> select($query);
$a_avg=$rows[0]['m']*$calibration;
$a_std=$rows[0]['d']*$calibration;
echo "a media=".$a_avg.", std=".$a_std;

//Calcolo media e std Wave 'c'
$query="SELECT avg(CSA) as m, std(csa) as d from sonogram inner join us_jvp on (sonogram.number=us_jvp.number and sonogram.videoclipid=us_jvp.videoclipid) WHERE (us_jvp.acxvyWave='c' and sonogram.videoclipid=".$vid.")";
$rows = $db -> select($query);
$c_avg=$rows[0]['m']*$calibration;
$c_std=$rows[0]['d']*$calibration;
echo "c media=".$c_avg.", std=".$c_std;

//Calcolo media e std Wave 'x'
$query="SELECT avg(CSA) as m, std(csa) as d from sonogram inner join us_jvp on (sonogram.number=us_jvp.number and sonogram.videoclipid=us_jvp.videoclipid) WHERE (us_jvp.acxvyWave='x' and sonogram.videoclipid=".$vid.")";
$rows = $db -> select($query);
$x_avg=$rows[0]['m']*$calibration;
$x_std=$rows[0]['d']*$calibration;
echo "x media=".$x_avg.", std=".$x_std;

//Calcolo media e std Wave 'v'
$query="SELECT avg(CSA) as m, std(csa) as d from sonogram inner join us_jvp on (sonogram.number=us_jvp.number and sonogram.videoclipid=us_jvp.videoclipid) WHERE (us_jvp.acxvyWave='v' and sonogram.videoclipid=".$vid.")";
$rows = $db -> select($query);
$v_avg=$rows[0]['m']*$calibration;
$v_std=$rows[0]['d']*$calibration;
echo "v media=".$v_avg.", std=".$v_std;

//Calcolo media e std Wave 'y'
$query="SELECT avg(CSA) as m, std(csa) as d from sonogram inner join us_jvp on (sonogram.number=us_jvp.number and sonogram.videoclipid=us_jvp.videoclipid) WHERE (us_jvp.acxvyWave='y' and sonogram.videoclipid=".$vid.")";
$rows = $db -> select($query);
$y_avg=$rows[0]['m']*$calibration;
$y_std=$rows[0]['d']*$calibration;
echo "v media=".$v_avg.", std=".$v_std;



//Calcolo Ritardo tra JVP/ECG per Ultrasound
//1) trova number onda P nP
//2) Da quel number trova number onde a,c,x,v,y: na, nc, nx, nv, ny
//3) Calcola intervalli nP-na, nP-nx, nP- ny. Lo stesso per nR
$dap=0;
$na=0;
$periodo=0;
$xp=0;
$nx=0;
$dxp=0;

$queryE="SELECT *  FROM  us_ecg  WHERE PQRSTwave='P' and us_ecg.videoclipid =".$vid;
$ers = $db->select($queryE);

$rn = count($ers,COUNT_NORMAL);

for ($i=0; $i<$rn-1; $i++) {
	$si=$ers[$i]['number'];
	$sf=$ers[$i+1]['number'];
	$periodo=$sf+1-$si;
	$queryJ="SELECT *  FROM  us_jvp  WHERE number>=".$si." and number<".$sf." and us_jvp.videoclipid =".$vid;
	$jrs = $db-> select($queryJ);
	$en = count($jrs,COUNT_NORMAL);
	for ($j=0; $j<$en; $j++) {
		$wave=$jrs[$j]['acxvyWave'];
		$jn=$jrs[$j]['number'];
		if($wave=="a"){
			$na+=1;
			$dap+=($jn-$si)/$periodo;
		}
		if($wave=="x"){
			$nx+=1;
			$dxp+=($jn-$si)/$periodo;
		}
	}
}

$dap=($dap/$na);
$dxp=($dxp/$nx);
echo "DELTA P-a=".$dap."DELTA P-x=".$dxp ;


//Calcolo Ritardo tra JVP/ECG per Cruenta
$c_dap=0;
$c_na=0;
$c_periodo=0;
$c_xp=0;
$c_dxp=0;
$c_nx=0;

$sid=1;//Da modificare
$queryE="SELECT *  FROM  cvp_ecg  WHERE PQRSTwave='P' and cvp_ecg.idscreenshot =".$sid;
$ers = $db->select($queryE);
$rn = count($ers,COUNT_NORMAL);
for ($i=0; $i<$rn-1; $i++) {
	$si=$ers[$i]['number'];
	$sf=$ers[$i+1]['number'];
	$c_periodo=$sf+1-$si;
	$queryJ="SELECT *  FROM  cvp_waves  WHERE number>=".$si." and number<".$sf." and cvp_waves.idscreenshot =".$sid;
	$jrs = $db-> select($queryJ);
	$en = count($jrs,COUNT_NORMAL);
	for ($j=0; $j<$en; $j++) {
		$wave=$jrs[$j]['acxvyWave'];
		$jn=$jrs[$j]['number'];
		if($wave=="a"){
			$c_na+=1;
			$c_dap+=($jn-$si)/$c_periodo;
		}
		if($wave=="x"){
			$c_nx+=1;
			$c_dxp+=($jn-$si)/$c_periodo;
		}
	}
}

$c_dap=($c_dap/$c_na);
$c_dxp=($c_dxp/$c_nx);
echo "DELTA P-a=".$c_dap."DELTA P-x=".$c_dxp ;


//Preparazione dati per plot ECG
$query="SELECT sonogram.number as n, CSA,us_ecg.PQRSTwave as ecg FROM `sonogram` left  join us_ecg on (sonogram.videoclipid=us_ecg.videoclipid and sonogram.number=us_ecg.number) where sonogram.videoclipid=".$vid;
$ecgrows = $db -> select($query);
$rn = count($ecgrows,COUNT_NORMAL);

for ($i=0; $i<$rn; $i++) {
	$myecg[$i]['x']='';
	$myecg[$i]['n']=$ecgrows[$i]['n']*$tc;	
	
	if(is_null($ecgrows[$i]['ecg']))
		$myecg[$i]['w']=$min;
	if($ecgrows[$i]['ecg']=='R')
		$myecg[$i]['w']=$max;
	if($ecgrows[$i]['ecg']=='P')
		$myecg[$i]['w']=$min+$delta/2;
	if($ecgrows[$i]['ecg']=='T')
		$myecg[$i]['w']=$min+$delta/4;
}

//var_dump($myecg);



$query="select '',number*effectiveDuration/numberOfFrames as time,round(csa*phdx*phsx,2) as CSA from sonogram inner join us_videoclip on sonogram.videoclipid=us_videoclip.videoclipid where sonogram.videoclipid=".$vid;

//echo $query;
$rows = $db -> select($query);
$rn = count($rows,COUNT_NORMAL);
//var_dump($rows);

$data=$rows;
$plot = new PHPlot(800, 300);
$plot->SetFailureImage(False); // No error images
$plot->SetPrintImage(False); // No automatic output
$plot->SetImageBorderType('plain');
$plot->SetPlotType('lines');
$plot->SetDataType('data-data');
$plot->SetDataColors(array('blue'));
$plot->SetDataValues($data);
$plot->SetPlotAreaWorld(0, $min-$delta/8, $tmax, $max);
$plot->DrawGraph();
$plot->SetDataColors(array('red'));
$plot->SetDataValues($myecg);
$plot->DrawGraph();
# Main plot title:
$plot->SetTitle('Juguale Venous Pulse:'.$rn);
# Make sure Y axis starts at 0:
$plot->SetPlotAreaWorld(0, $min-$delta/8, $tmax, $max);
$plot->DrawGraph();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
     "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>PHPlot Example - Inline Image</title>
</head>
<body>
<header><small><a href='index.php'>Home</a><a href='controller.php?action=loadStudy&study=<?php echo $suid;?>'> back</a></small></heder>
<div style="text-align: center">
<table align=center>
<tr>
<th>SOGGETTO</th><th>DATA DI NASCITA</th><th>DATA ESAME</th><th>RECAPITO TELEFONICO</th>
</tr>
<tr>
<td></td><td></td><td></td><td></td>
</tr>
</table>
</div>
<h1 style="text-align: center">ANALISI ULTRASONORA NON INVASIVADEL RITORNO VENOSO CEREBRALE E DEL POLSO GIUGULARE</h1>
<img src="<?php echo $plot->EncodeImage();?>" alt="Plot Image">
<p>
<b>Storia: </b>
</p>
<p>
<b>Quesito clinico: </b>
</p>
<p><b>CCA, ICA, ECA, AV</b> Arteria Carotide Comune, A. C. Interna, A. C. Esterna, A. Vertebrale<br>
Parametri emodinamici :
Flusso totale in ingresso alla testa (<b>HBinF</b>) e flusso totale in ingresso al cervello (<b>CBF</b>)
</p>
<table align=center>
<tr>
<th>HBinF</th><th>HBinF</th><th>CBF</th><th>CBF</th>
</tr>
<tr>
<td>cm<sup>3</sup>/s</td><td>ml/min</td><td>cm<sup>3</sup>/s</td><td>ml/min</td>
</tr>
<tr>
<td></td><td></td><td></td><td></td>
</tr>
</table>
<p><b>IJV DX J2 0</b> Vena Giugulare Interna, Destra, Settore J2, Supino</p><br>
Blocchi di flusso  Valvola ipomobile (M-Mode)<br>
Flusso bidirezionale   Compressioni ab estrinseco<br>
Tracciato del polso giugulare

<br>
<b>Parametri emodinamici</b> :<br>
Area di sezione (<b>CSA</b>), velocità del sangue (<b>TAV</b>) e flusso sanguigno (<b>Q</b>)
<table align=center>
<tr>
<th>CSA<sub>mean</sub></th><th>CSA<sub>max</sub></th><th>CSA<sub>min</sub></th><th>TAV<sub>mean</sub></th><th>TAV<sub>peack<sub></th>
</tr>
<tr>
<td>cm<sup>2</sup></td><td>cm<sup>2</sup></td><td>cm<sup>2</sup></td><td>cm/s</td><td>cm/s</td>
</tr>
<tr>
<td></td><td></td><td></td><td></td><td></td>
</tr>
</table>


</body>
</html>

