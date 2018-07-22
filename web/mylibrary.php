<!--
Questo software è composto da una web application in PHP su MySQL. Lo scopo 
è quello di amministtrare i dati raccolti durante le indgini diagnostiche del 
Centro di Malattie Vascolari dell'università di Ferrara.
    Copyright (C) <2017>  <Francesco Sisini>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
-->
<?php
require_once 'db.php';

function stats($arr){
	$rn = count($arr,COUNT_NORMAL);
	$mu=0;	
	for($i=0;$i<$rn;$i++){
		$mu+=$arr[$i]['value'];
	}
	$mu=$mu/$rn;
	$sd=0;
	for($i=0;$i<$rn;$i++){
		$a=($arr[$i]['value']-$mu);
		$sd+=$a*$a;
	}
	$sd=sqrt($sd/($rn-1));
	$stat[0]['mu']=$mu;
	$stat[0]['sd']=$sd;
	return $stat;
}


function getUSSelectiveECGwaves($wave,$vid){
	$db = new Db();
	//Carica l'ecg degli ultrasuoni
	$queryE="SELECT *  FROM  us_ecg  WHERE PQRSTwave='".$wave."' and us_ecg.videoclipid =".$vid." order by number";
	$ers = $db->select($queryE);
	$rn = count($ers,COUNT_NORMAL);
	return $ers;
}

function getUSSelectiveJVPwaves($wave,$vid){
	$db = new Db();
	//Carica l'ecg degli ultrasuoni
	$queryE="SELECT *  FROM  us_jvp  WHERE acxvyWave='".$wave."' and us_jvp.videoclipid =".$vid." order by number";
	$ers = $db->select($queryE);
	$rn = count($ers,COUNT_NORMAL);
	return $ers;
}


function getCVPid($suid)
{
	$db = new Db();
	$query="SELECT cvpid FROM `cvp_examination` WHERE StudyInstanceUID='$suid'";
	$ers = $db->select($query);
	$rn = count($ers,COUNT_NORMAL);
	if($rn==0) return -1;
	$cid= $ers[0]['cvpid'];
	return $cid;
}
function getScreenshot($cvpid){
	$db = new Db();
	//Carica l'ecg degli ultrasuoni
	$query="SELECT * FROM  screenshot  WHERE cvpid=$cvpid";
	$rs = $db->select($query);
	$rn = count($rs,COUNT_NORMAL);
	return $rs;
}
function isWavesForScreenshot($waveType,$idscreenshot)
{
	$db = new Db();
	if($waveType=="waves")
	{
		$query="SELECT * FROM `cvp_waves` WHERE idscreenshot=$idscreenshot";
	}
	if($waveType=="ecg")
	{
		$query="SELECT * FROM `cvp_ecg` WHERE idscreenshot=$idscreenshot";
	}
	if($waveType=="cvp")
	{
		$query="SELECT * FROM `cvp_sampling` WHERE idscreenshot=$idscreenshot";
	}
	
	$ers = $db->select($query);
	$rn = count($ers,COUNT_NORMAL);
	if($rn==0) return false;
	return true;
}


function getCVPSelectiveECGwaves($wave,$sid){
	$db = new Db();
	//Carica l'ecg degli ultrasuoni
	if($wave!="*"){
		$queryE="SELECT *  FROM  cvp_ecg  WHERE PQRSTwave='".$wave."' and cvp_ecg.idscreenshot =".$sid." order by number";
	}	
	else
	{
		$queryE="SELECT *  FROM  cvp_ecg  WHERE cvp_ecg.idscreenshot =".$sid." order by number";
	}
	
	$ers = $db->select($queryE);
	$rn = count($ers,COUNT_NORMAL);
	//echo $queryE;
	return $ers;
}
function getDopplerSelectiveECGwaves($wave,$did){
	$db = new Db();
	//Carica l'ecg degli ultrasuoni
	if($wave!="*"){
		$queryE="SELECT *  FROM  doppler_ecg  WHERE PQRSTwave='".$wave."' and iddoppler =".$did." order by number";
	}else
	{
		$queryE="SELECT *  FROM  doppler_ecg  WHERE  iddoppler =".$did." order by number";
	}
	$ers = $db->select($queryE);
	$rn = count($ers,COUNT_NORMAL);
	return $ers;
}





function isDopplerECG($did)
{
	$db = new Db();
	//Carica l'ecg degli ultrasuoni
	$queryE="SELECT *  FROM  doppler_ecg  WHERE  iddoppler =".$did." order by number";

	$ers = $db->select($queryE);
	$rn = count($ers,COUNT_NORMAL);
	return $rn>0;
}



function getDopplerData($did){
	$db = new Db();
	$query="SELECT * FROM `doppler_sampling` WHERE iddoppler=".$did;
	$rr=$db->select($query);
	$rn = count($rr,COUNT_NORMAL);
	$dopp[0]['z']="";
	$dopp[0]['x']="";
	$dopp[0]['y']="";
	for($i=0;$i<$rn;$i++){
		$dopp[$i]['z']="";
		$dopp[$i]['x']=$rr[$i]['number'];
		$dopp[$i]['y']=getCalibratedDoppler($rr[$i]['meanVelocity'],$did);
	}
	return $dopp;

}
function getCVPData($sid){
$db = new Db();
	$query="SELECT * FROM `cvp_sampling` WHERE idscreenshot=".$sid;
	$rr=$db->select($query);
	$rn = count($rr,COUNT_NORMAL);
	$dopp[0]['z']="";
	$dopp[0]['x']="";
	$dopp[0]['y']="";
	for($i=0;$i<$rn;$i++){
		$dopp[$i]['z']="";
		$dopp[$i]['x']=$rr[$i]['number'];
		$dopp[$i]['y']=getCalibratedCVP($rr[$i]['pressure'],$sid);
	}
	return $dopp;

}

function getCalibratedCVPstats($wave,$sid){
	$db = new Db();
	$query="SELECT avg(pressure) as m, std(pressure) as d FROM `cvp_sampling` inner join cvp_waves on cvp_waves.number=cvp_sampling.number and cvp_waves.idscreenshot=cvp_sampling.idscreenshot  WHERE acxvyWave='$wave' and  cvp_sampling.idscreenshot=".$sid ;
	//echo $query;
	$rows = $db -> select($query);
	$m=getCalibratedCVP($rows[0]['m'],$sid);
	$cal=getCVPCalibration($sid);
	//var_dump($cal);
	$d=$rows[0]['d']/$cal['f'];
	$jvps=array("m"=>$m,"d"=>$d);
	return $jvps;
}

function getCVPCalibration($sid){
	$db = new Db();
	$pq="SELECT pix0 , pix10 FROM `screenshot` WHERE idscreenshot=".$sid;
	$rr=$db->select($pq);
	$rn = count($rr,COUNT_NORMAL);
	if($rn>0)
	{
		$p0=$rr[0]['pix0'];
		$p10=$rr[0]['pix10'];
	}else{
		//Come gestiamo?
		$error=true;
	}
	$press2pix=($p0-$p10)/10;
	$cal=array("offset"=>$p0,"f"=>$press2pix);
	return $cal;
}


/**
Si assume che P10 sia rilevato in corrispondenza del valore 10
*/
function getCalibratedCVP($rawValue,$sid)
{
	$error=false;
	
	$db = new Db();
	$pq="SELECT pix0 , pix10 FROM `screenshot` WHERE idscreenshot=".$sid;
	$rr=$db->select($pq);
	$rn = count($rr,COUNT_NORMAL);
	if($rn>0)
	{
		$p0=$rr[0]['pix0'];
		$p10=$rr[0]['pix10'];
	}else{
		//Come gestiamo?
		$error=true;
	}
	$press2pix=($p0-$p10)/10;
	if($error)return false;
	$calibrated=($p0-$rawValue)/$press2pix;
	return $calibrated;

}


function getCalibratedDoppler($rawValue,$did)
{
	$error=false;
	
	$db = new Db();
	$pq="SELECT pixelTocms as p2c, baseLine as bl FROM `us_doppler` WHERE iddoppler=".$did;
	$rr=$db->select($pq);
	$rn = count($rr,COUNT_NORMAL);
	if($rn>0)
	{
		$p2c=$rr[0]['p2c'];
		$bl=$rr[0]['bl'];
	}else{
		//Come gestiamo?
		$error=true;
	}
	if($error)return false;
	$calibrated=($bl-$rawValue)/$p2c;
	return $calibrated;

}

function getCalculatedFlowData($suid,$rl,$jp,$csaCycleID,$dopplerCycleID){
	$error=false;
	$db = new Db();
	//Determina il video di riferimento per le CSA
	$vid=getVideoId($suid,$rl,$jp);
		//Determina la calibrazione
	$cal=getCalibration($vid);
	$calibration=$cal['calibration'];
	$tc=$cal['tc'];

	$usprows=getUSSelectiveECGwaves("P",$vid);
	$rn = count($usprows,COUNT_NORMAL);
	$up1=0;$up2=0;
	if($rn<$csaCycleID+1)
	{
		$error=true;
	}else
	{	
		$up1=$usprows[$csaCycleID-1]['number'];
		$up2=$usprows[$csaCycleID]['number'];
		$periodoU=$up2-$up1;
	}
	$usq="SELECT number , csa  FROM `sonogram` WHERE number>=".$up1." and number<".$up2." and videoclipid=".$vid;
	$usr=$db->select($usq);

	//Determina il Doppler ID
	$did=getDopplerId($suid,$rl,$jp);
	if($did==-1)
	{
		return null;
	}
	//Identifica un ciclo cardiaco
	$rciclo=getDopplerSelectiveECGwaves("P",$did);
	$rn = count($rciclo,COUNT_NORMAL);
	if($rn<$dopplerCycleID+1)
	{
		$error=true;
		return null;
	}else
	{	
		$p1=$rciclo[$dopplerCycleID-1]['number'];
		$p2=$rciclo[$dopplerCycleID]['number'];
		$periodoD=$p2-$p1;
	}
	$mq="SELECT number as x, meanVelocity as y FROM `doppler_sampling` WHERE number>=".$p1." and number<".$p2." and iddoppler=".$did;
	//echo $mq;
	//die();
	$rr=$db->select($mq);
	$resampled=resample($rr);
	//Calcola l'array prodotto del doppler con la CSA
		//1) Calcola quanti campionamenti US corrispondono ad un campionamento Doppler
		//2)
	$u_in_d_units=$periodoU/$periodoD;
	for($k=0;$k<$periodoU;$k++)
	{	
		$id=$k/($u_in_d_units);
		$flow[$k]['z']="";
		$flow[$k]['t']=$k/$periodoU;
		$flow[$k]['q']=$usr[$k]['csa']*$calibration*(getCalibratedDoppler($resampled[$id]['y'],$did));
	}
	return $flow;
}
function resample($rows){
	//Si considera che i dati siano in serie x,y
	$n=count($rows,COUNT_NORMAL);
	$lastx=0;
	$inx=0;
	for($i=0;$i<$n;$i++){
		$x=$rows[$i]['x'];
		$y=$rows[$i]['y'];
		for($j=$lastx; $j<$x;$j++)
		{
			$samp[$inx]['x']=$j;
			$samp[$inx]['y']=$y;
			$inx++;
		}

	}
	return $samp;
}

//Restituisce l'id dello studio doppler. -1 se non è presente.
function getDopplerId($suid,$rl,$jp){
	$db = new Db();
	$rowStudy = $db -> select("SELECT * FROM `us_doppler` WHERE Jposition123=".$jp." and RightOrLeftIJV='".$rl."' and studyInstanceUID=".$db->quote($suid));
	$rn = count($rowStudy,COUNT_NORMAL);
	if($rn==0) return -1;
	$did= $rowStudy[0]['iddoppler'];
	return $did;
}

function getScreenshotId($cvpID){
	$db = new Db();
	$rowS = $db -> select("SELECT * FROM `screenshot` WHERE cvpid=".$db->quote($cvpID));
	$rn = count($rowS,COUNT_NORMAL);
	if($rn==0) return -1;
	$sid= $rowS[0]['idscreenshot'];
	return $sid;
}


function getVideoId($suid,$rl,$jp){
	$db = new Db();
	$query="SELECT * from us_videoclip where RightOrLeftIJV='".$rl."' and Jposition123=".$jp." and  studyInstanceUID =".$db->quote($suid);
	$rowStudy = $db -> select($query);
	$rn = count($rowStudy,COUNT_NORMAL);
    if($rn==0)
        return -1;
	$vid= $rowStudy[0]['videoclipID'];
	return $vid;
}
function getTraceParameter($vid)
{
	$db = new Db();
	$query="select round(std(csa*phdx*phsx),2) as std, round(max(csa*phdx*phsx),2) as max, round(min(csa*phdx*phsx),2) as min, 	round(avg(csa*phdx*phsx),2) as avg, max(number*effectiveDuration/numberOfFrames) as tmax  from sonogram  inner join 		us_videoclip on sonogram.videoclipid=us_videoclip.videoclipid where sonogram.videoclipid=".$vid;
	//echo $query;
	$rows = $db -> select($query);
	$min=$rows[0]['min'];
	$max=$rows[0]['max'];
	$avg=$rows[0]['avg'];
	$std=$rows[0]['std'];
	$tmax=$rows[0]['tmax'];
	$lim=array("std"=>$std,"max"=>$max,"min"=>$min,"avg"=>$avg,"tmax"=>$tmax);
	return $lim;
}

function getCalibration($vid){
	$db = new Db();
	$calibration=0;
	$query="select  phdx*phsx as calibration, effectiveDuration/numberOfFrames as tc  from  us_videoclip  where videoclipid=".$vid;
	$rows = $db -> select($query);
	$rn = count($rows,COUNT_NORMAL);
	if($rn==0) die("...Problem...".$query);
	$cal=array("calibration"=>$rows[0]['calibration'],"tc"=>$rows[0]['tc']);
	return $cal;
}
function getUncalibratedJVPstats($vid,$wave){
	$db = new Db();
	$query="SELECT avg(CSA) as m, std(csa) as d from sonogram inner join us_jvp on (sonogram.number=us_jvp.number and 	sonogram.videoclipid=us_jvp.videoclipid) WHERE (us_jvp.acxvyWave='".$wave."' and sonogram.videoclipid=".$vid.")";
	//echo $query;
	$rows = $db -> select($query);
	$jvps=array("m"=>$rows[0]['m'],"d"=>$rows[0]['d']);
	return $jvps;
}




function getECGplottableData($vid,$waves="PRT"){
	$db = new Db();
	$limit=getTraceParameter($vid);
	$max=$limit["max"];
	$min=$limit["min"];
	$delta=$max-$min;
	$cal=getCalibration($vid);
	$tc=$cal['tc'];
	$R= (preg_match('/R/',$waves));
	$P= (preg_match('/P/',$waves));
	$T=(preg_match('/T/',$waves));	


	$query="SELECT sonogram.number as n, CSA,us_ecg.PQRSTwave as ecg FROM `sonogram` left  join us_ecg on (sonogram.videoclipid=us_ecg.videoclipid and sonogram.number=us_ecg.number) where sonogram.videoclipid=".$vid;
	$ecgrows = $db -> select($query);
	$rn = count($ecgrows,COUNT_NORMAL);
	for ($i=0; $i<$rn; $i++) {
		$myecg[$i]['x']='';
		$myecg[$i]['n']=$ecgrows[$i]['n']*$tc;	
		
		if(is_null($ecgrows[$i]['ecg']))
			$myecg[$i]['w']=$min;
		if($ecgrows[$i]['ecg']=='R')
		{
			if($R)			
				$myecg[$i]['w']=$max;
			else
				$myecg[$i]['w']=$min;
		}		
		if($ecgrows[$i]['ecg']=='P'){
			if($P)
				$myecg[$i]['w']=$min+$delta/2;
			else
				$myecg[$i]['w']=$min;
		}
		if($ecgrows[$i]['ecg']=='T')
			if($T)
				$myecg[$i]['w']=$min+$delta/4;
			else
				$myecg[$i]['w']=$min;
	}
	if(empty($myecg))$myecg=null;
	return $myecg;
}

function getJVPplottableData($vid){
	$db = new Db();
	$limit=getTraceParameter($vid);
	$max=$limit["max"];
	$min=$limit["min"];
	$delta=$max-$min;
	$cal=getCalibration($vid);
	$tc=$cal['tc'];
	

	$query="SELECT sonogram.number as n, CSA,us_jvp.acxvyWave as jvp FROM `sonogram` left  join us_jvp on (sonogram.videoclipid=us_jvp.videoclipid and sonogram.number=us_jvp.number) where sonogram.videoclipid=$vid";
	$jvprows = $db -> select($query);
	$rn = count($jvprows,COUNT_NORMAL);
	for ($i=0; $i<$rn; $i++) {
		$myjvp[$i]['x']='';
		$myjvp[$i]['n']=$jvprows[$i]['n']*$tc;	
		
		if(is_null($jvprows[$i]['jvp']))
			$myjvp[$i]['w']=$min;
		if($jvprows[$i]['jvp']=='a')
			$myjvp[$i]['w']=$max;
		if($jvprows[$i]['jvp']=='c')
			$myjvp[$i]['w']=$min+$delta/2;
		if($jvprows[$i]['jvp']=='v')
			$myjvp[$i]['w']=$min+$delta/4;
		if($jvprows[$i]['jvp']=='x')
			$myjvp[$i]['w']=$min+$delta/6;
		if($jvprows[$i]['jvp']=='y')
			$myjvp[$i]['w']=$min+$delta/8;
	}
	return $myjvp;
}




function getCalibratedJVP($vid){
	$db = new Db();
	$query="select '',number*effectiveDuration/numberOfFrames as time,round(csa*phdx*phsx,6) as CSA from sonogram inner join 	us_videoclip on sonogram.videoclipid=us_videoclip.videoclipid where sonogram.videoclipid=".$vid;
	$rows = $db -> select($query);
	$rn = count($rows,COUNT_NORMAL);
	return $rows;
}
//Calcola l'Inflow in cm^2/3
function getInFlow($suid)
{
	$r_in=0;
	$r_cbf=0;
	$db = new Db(); 
	$suid=$_GET['study'];
	$query="SELECT * from us_report inner join us_study on us_report.studyInstanceUID=us_report.studyInstanceUID where 		us_report.studyInstanceUID =".$db->quote($suid);
	$rows = $db -> select($query);
	$rn = count($rows,COUNT_NORMAL);
	if($rn>0){

		//Lato destro
		$r_in+=$rows[0]['cca_csa_d']*$rows[0]['cca_v_d'];
		$r_in+=$rows[0]['ica_csa_d']*$rows[0]['ica_v_d'];
		$r_in+=$rows[0]['eca_csa_d']*$rows[0]['eca_v_d'];
		$r_in+=$rows[0]['av_csa_d']*$rows[0]['av_v_d'];
		$r_cbf+=$rows[0]['ica_csa_d']*$rows[0]['ica_v_d'];
		$r_cbf+=$rows[0]['av_csa_d']*$rows[0]['av_v_d'];
		//Lato sinistro
		$r_in+=$rows[0]['cca_csa_s']*$rows[0]['cca_v_s'];
		$r_in+=$rows[0]['ica_csa_s']*$rows[0]['ica_v_s'];
		$r_in+=$rows[0]['eca_csa_s']*$rows[0]['eca_v_s'];
		$r_in+=$rows[0]['av_csa_s']*$rows[0]['av_v_s'];
		$r_cbf+=$rows[0]['ica_csa_s']*$rows[0]['ica_v_s'];
		$r_cbf+=$rows[0]['av_csa_s']*$rows[0]['av_v_s'];
		$infl=array("hbinf"=>$r_in,"cbf"=>$r_cbf);
	return $infl;
	}else
	{
		return null;
	}

}

function chk($tag){
	if($tag==1) return "checked";
	return "unchecked";
}
function W_GET($par){
	if(array_key_exists(  $par ,  $_GET ))
	{
		return $_GET[$par];
	}
	return "";
}

?>

