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
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'db.php';
//echo $_GET['action']; 
$a= $_GET['action'];

if($a=='deleteSonogram'){
	$db = new Db();
	$video=$_GET['video'];
	$SUID=$_GET['study'];
	$db = new Db();
	$query="delete from `sonogram` where videoclipid=".$video;
	$db->query($query);
	Redirect("loadStudy.php?study=$SUID");
}
if($a=='deleteSonogramECG'){
	$db = new Db();
	$video=$_GET['video'];
	$SUID=$_GET['study'];
	$db = new Db();
	$query="delete from `us_ecg` where videoclipid=".$video;
	$db->query($query);
	Redirect("loadStudy.php?study=$SUID");
}
if($a=='insertrows'){
	$video=$_GET['video'];
	$type=$_GET['type'];
	$SUID=$_GET['study'];
	$data=$_POST['data'];
	
	$db = new Db(); 
	if($type=='csa')
	{
		$rows=explode(PHP_EOL,$data);
		//var_dump($rows);
		$rn=count($rows,COUNT_NORMAL);
		for($i=0;$i<$rn;$i++)
		{
			$str=str_replace("\t",",",$rows[$i]);
			$str=trim(str_replace(";",",",$str));
			if($str!=''){
				$query="INSERT INTO `sonogram`(`videoclipID`, `processID`, `number`, `csa`, `perimeter`) VALUES";
				$query=$query."($video,1,$str)";
				$db->query($query);
				//echo $query;
			}
		}
	}
	if($type=='ecg')
	{
		$rows=explode(PHP_EOL,$data);
		//var_dump($rows);
		$rn=count($rows,COUNT_NORMAL);
		for($i=0;$i<$rn;$i++)
		{
			$str=str_replace("\t",",'",$rows[$i]);
			$str=trim(str_replace(";",",'",$str));
			if($str!=''){
				$query="INSERT INTO `us_ecg`(`videoclipID`, `number`, `PQRSTwave`) VALUES ";
				$query=$query."($video,$str')";
				$db->query($query);
				//echo $query;
			}
		}
	}
	if($type=='doppler')
	{
		$did=$_GET['doppler'];
		$rows=explode(PHP_EOL,$data);
		//var_dump($rows);
		$rn=count($rows,COUNT_NORMAL);
		for($i=0;$i<$rn;$i++)
		{
			$str=str_replace("\t",",",$rows[$i]);
			$str=trim(str_replace(";",",",$str));
			if($str!=''){
				$query="INSERT INTO `doppler_sampling`(`iddoppler`, `number`, `meanVelocity`) VALUES ";
				$query=$query."($did,$str)";
				$db->query($query);
				//echo $query;
			}
		}
	}
	if($type=='dopplerECG')
	{
		$did=$_GET['doppler'];
		$rows=explode(PHP_EOL,$data);
		//var_dump($rows);
		$rn=count($rows,COUNT_NORMAL);
		for($i=0;$i<$rn;$i++)
		{
			$str=str_replace("\t",",'",$rows[$i]);
			$str=trim(str_replace(";",",'",$str));
			if($str!=''){
				$query="INSERT INTO `doppler_ecg`(`iddoppler`, `number`, `PQRSTwave`) VALUES ";
				$query=$query."($did,$str')";
				$db->query($query);
				//echo $query;
			}
		}
	}
	if($type=='jvp')
	{
		
		$rows=explode(PHP_EOL,$data);
		//var_dump($rows);
		$rn=count($rows,COUNT_NORMAL);
		for($i=0;$i<$rn;$i++)
		{
			$str=str_replace("\t",",'",$rows[$i]);
			$str=trim(str_replace(";",",'",$str));
			if($str!=''){
				$query="INSERT INTO `us_jvp`(`videoclipID`, `number`, `acxvyWave`) VALUES ";
				$query=$query."($video,$str')";
				$db->query($query);
				//echo $query;
			}
		}
	}
	
	
	if($type=='cvpwaves')
	{
		$ids=$_GET['idscreenshot'];
		$rows=explode(PHP_EOL,$data);
		//var_dump($rows);
		$rn=count($rows,COUNT_NORMAL);
		for($i=0;$i<$rn;$i++)
		{
			$str=str_replace("\t",",'",$rows[$i]);
			$str=trim(str_replace(";",",'",$str));
			if($str!=''){
				$query="INSERT INTO `cvp_waves`(`idscreenshot`, `number`, `acxvyWave`) VALUE ";
				$query=$query."($ids,$str')";
				$db->query($query);
				//echo $query;
			}
		}
	}
	if($type=='cvpecg')
	{
		$ids=$_GET['idscreenshot'];
		$rows=explode(PHP_EOL,$data);
		//var_dump($rows);
		$rn=count($rows,COUNT_NORMAL);
		for($i=0;$i<$rn;$i++)
		{
			$str=str_replace("\t",",'",$rows[$i]);
			$str=trim(str_replace(";",",'",$str));
			if($str!=''){
				$query="INSERT INTO `cvp_ecg`(`idscreenshot`, `number`, `PQRSTwave`) VALUE ";
				$query=$query."($ids,$str')";
				$db->query($query);
				//echo $query;
			}
		}
	}
	if($type=='cvpcvp')
	{
		$ids=$_GET['idscreenshot'];
		$rows=explode(PHP_EOL,$data);
		//var_dump($rows);
		$rn=count($rows,COUNT_NORMAL);
		for($i=0;$i<$rn;$i++)
		{
			$str=str_replace("\t",",'",$rows[$i]);
			$str=trim(str_replace(";",",'",$str));
			if($str!=''){
				$query="INSERT INTO `cvp_sampling`(`idscreenshot`, `number`, `pressure`) VALUE ";
				$query=$query."($ids,$str')";
				$db->query($query);
				//echo $query;
			}
		}
	}
	
	Redirect("loadStudy.php?study=$SUID");
}

if($a=='addCruenta'){
	$study=$_GET['study'];
	$idscreenshot=$_GET['idscreenshot'];
	$type=$_GET['type'];
	Redirect("inputData.php?study=$study&type=$type&idscreenshot=$idscreenshot");
}
if($a=='addCSA'){
	$video=$_GET['video'];
	$study=$_GET['study'];
	Redirect("inputData.php?video=$video&study=$study&type=csa");
}
if($a=='addCVP'){
	$study=$_GET['study'];
	Redirect("inputData.php?study=$study&type=waves");
}
if($a=='addECG'){
	$video=$_GET['video'];
	$study=$_GET['study'];
	Redirect("inputData.php?video=$video&study=$study&type=ecg");
}
if($a=='addJVP'){
	$video=$_GET['video'];
	$study=$_GET['study'];
	Redirect("inputData.php?video=$video&study=$study&type=jvp");
}
if($a=='addDoppler'){
	$video=$_GET['video'];
	$study=$_GET['study'];
	$did=$_GET['doppler'];
	Redirect("inputData.php?video=$video&study=$study&type=doppler&doppler=$did");
}
if($a=='addDopplerECG'){
	$video=$_GET['video'];
	$study=$_GET['study'];
	$did=$_GET['doppler'];
	Redirect("inputData.php?video=$video&study=$study&type=dopplerECG&doppler=$did");
}


if($a=='browse'){
	$path=$_GET['path'];
	$project=$_GET['project'];
	$mod=$_GET['mod'];
	if(!is_dir($path)){
		Redirect("DICOM.php?path=$path&project=$project&mod=$mod");
	}
	Redirect("browse.php?path=$path&project=$project&mod=$mod");
}


if($a=='addScreenshot'){
	$suid=$_GET['study'];
	$path=$_GET['path'];
	$project=$_GET['project'];
	$cvpid=$_GET['cvpid'];
	if(!is_dir($path)){
		Redirect("screenshot.php?path=$path&project=$project&mod=screenshot&cvpid=$cvpid&study=$suid");	
	}
	Redirect("browse.php?path=$path&project=$project&mod=screenshot&cvpid=$cvpid&study=$suid");
}

if($a=='insertScreenshot'){
	$db = new Db(); 
	$cvpid=$_GET['cvpid'];
	$fname=$_POST['fname'];
	$fdate=$_POST['fdate'];
	$suid=$_GET['suid'];
	$pix0=$_POST['pix0'];
	$pix10=$_POST['pix10'];
	$query="INSERT INTO `screenshot`( `cvpID`, `data_shot`, `fileName`,`pix0`,`pix10`) VALUES ";
	$query=$query."($cvpid,'$fdate','$fname',$pix0,$pix10)";
	$db->query($query);
	Redirect("loadStudy.php?study=$suid");
}



if($a=='insertStudy'){
	$db = new Db(); 
	//var_dump($_POST);
	$project=$_POST['project'];
	$SUID=$_POST['SUID'];
	$pn=$_POST['pname'];
	$pid=$_POST['pid'];
	$sdate=$_POST['sdate'];
	$stime=$_POST['stime'];
	$isn=$_POST['isn'];
	$ctime=$_POST['ctime'];
	$rl=$_POST['rl'];
	$j123=$_POST['j123'];
	$phdx=$_POST['phdx'];
	$phdy=$_POST['phdy'];
	$umx=$_POST['pux'];
	$umy=$_POST['puy'];
	$eduration=$_POST['eduration'];
	$nof=$_POST['nof'];
	$fname=$_POST['fname'];
	$mod=$_GET['mod'];

	$ctime=toSQLtime($ctime);
	$stime=toSQLtime($stime);
	$sdate=toSQLdate($sdate);

	if($mod=='bmode')
	//INSERISCE UN VIDEO B MODE
	{
		$query="SELECT * FROM `us_study` WHERE StudyInstanceUID='$SUID'";
		$rs=$db->select($query);
		$rn=count($rs,COUNT_NORMAL);
		if($rn==0){
			//Inserisce lo studio prima del video
			$query="INSERT INTO `us_study`(`studyInstanceUID`, `patientName`, `patientFamilyName`, `patientID`,";
			$query=$query." `studyDateTime`,`dataEntryDateTime`, `researchID`) VALUES ";
		 	$query=$query."('$SUID','$pn','','$pid','$sdate $stime',now(),'$project')";
			$db -> query($query);
			print($query);	
		}
	
		$query="Select * from us_videoclip where instanceNumber=$isn and studyInstanceUID='$SUID'";
		$rs=$db->select($query);
		$rn=count($rs,COUNT_NORMAL);
		if($rn==0)
		{
			$query="INSERT INTO `us_videoclip`( `instanceNumber`, `studyInstanceUID`, `dataOraVideo`, `dataEntryDateTime`,";
			$query=$query." `RightOrLeftIJV`, `Jposition123`, `phdx`, `umx`, `phsx`, `umy`, `effectiveDuration`, `numberOfFrames`,";
			$query=$query." `fileName`) VALUES ($isn,'$SUID','$ctime',now(),'$rl',$j123,$phdx,$umx,$phdy,$umy,$eduration,$nof,'$fname')";
		print($query);
		}
		$db -> query($query);
	}
	if($mod=='doppler'){
		//INSERISCE UN DOPPLER
		$ptc=1/$_POST['phdy'];
		$baseline=$_POST['baseline'];
		$query="INSERT INTO `us_doppler`( `studyInstanceUID`, `data_shot`, `RightOrLeftIJV`, `Jposition123`, `fileName`, `pixelTocms`, `baseLine`)"; 
		$query=$query."VALUES ('$SUID','$ctime','$rl','$j123','$fname',$ptc,$baseline)";
		$rs=$db->query($query);
	}
	if($mod=='cvp'){
		//INSERISCE UN DOPPLER
		$query="INSERT INTO `cvp_examination`( `studyInstanceUID`, `patientName`, `patientFamilyName`, `patientID`, `dataOraEsame`, `dataEntryDateTime`)"; 
		$query=$query."VALUES ('$SUID','$pn',' ','$pid',' ',now())";
		$rs=$db->query($query);
	}

	Redirect("loadStudy.php?study=$SUID");
}
if($a=='ceo'){
	Redirect("ceo.php");
}
if($a=='credits'){
	Redirect("credits.php");
}
if($a=='specifiche'){
	Redirect("specifiche.php");
}

if($a=='disclaimer'){
	Redirect("disclaimer.php");

}
if($a=='start'){
	Redirect("start.php");
}
if($a=='listAllstudies'){
	Redirect("listStudy.php?project=all");
}
if($a=='listAllreports'){
	Redirect("listReports.php?project=all");
}

if($a=='listStudies'){
	$project= $_GET['project'];
	Redirect("listStudy.php?project=".$project);
}
if($a=='loadStudy'){
	$study= $_GET['study'];
	Redirect("loadStudy.php?study=".$study);
}

if($a=='loadvideo'){
	$video= $_GET['video'];
	$study= $_GET['study'];
	Redirect("sonogram.php?video=".$video."&study=".$study);
}

if($a=='loadvideoEcg'){
	$video= $_GET['video'];
	$study= $_GET['study'];
	Redirect("sonogramECG.php?video=".$video."&study=".$study);
}
if($a=='loadvideoJVP'){
	$video= $_GET['video'];
	$study= $_GET['study'];
	Redirect("sonogramJVP.php?video=".$video."&study=".$study);
}

if($a=='plotDoppler'){
	$doppler= $_GET['doppler'];
	$study= $_GET['study'];
	Redirect("plotDoppler.php?doppler=$doppler&study=$study");
}
if($a=='plotCVP'){
	$screenshot= $_GET['idscreenshot'];
	$study= $_GET['study'];
	Redirect("plotCVP.php?screenshot=$screenshot&study=$study");
}


if($a=='medicalreport'){;
	$study= $_GET['study'];
	Redirect("medicalreport.php?study=".$study);
}
if($a=='researchreport'){;
	$study= $_GET['study'];
	Redirect("researchreport.php?study=".$study);
}
if($a=='getcvpdata'){
	$screenshot= $_GET['screenshot'];
	$mode= $_GET['mode'];
	Redirect("getcvpdata.php?screenshot=".$screenshot);
}
if($a=='getdata'){
	$video= $_GET['video'];
	$mode= $_GET['mode'];
	Redirect("getdata.php?video=".$video."&mode=".$mode);
}
if($a=='loadreport'){
	$study= $_GET['study'];
	Redirect("report.php?study=".$study);
}

if($a=='listsproject'){
	Redirect("listProject.php");
}

if($a=='uploadFile'){
	Redirect("./uploads/myupload.html");
}

if($a=='savereport'){
	saverReport();
	$study= $_GET['study'];
	Redirect("loadStudy.php?study=".$study);
}


function saverReport()
{
	//bool array_key_exists ( mixed $key , array $array )
	$db = new Db(); 
	//Eliminare il report esistente (se presente) con chiave esterna StudyInstanceUID
	$suid= $db -> quote($_GET['study']);
	$query="DELETE FROM us_report WHERE StudyInstanceUID=".$suid;
	$isok=$db -> query($query);//Come verifico se questa query è stata eseguita?
	if(!$isok) die(" <b>Error saving data. Click to come back <b><a href=controller.php?action=start>Home</a>");
	//Caricare i dati
	$storia=$db ->quote($_GET['storia']);
	$quesito=$db ->quote($_GET['quesito']);
	$cca_csa_d=$_GET['cca_csa_d'];
	$ica_csa_d=$_GET['ica_csa_d'];
	$eca_csa_d=$_GET['eca_csa_d'];
	$av_csa_d=$_GET['av_csa_d'];
	$cca_csa_s=$_GET['cca_csa_s'];
	$ica_csa_s=$_GET['ica_csa_s'];
	$eca_csa_s=$_GET['eca_csa_s'];
	$av_csa_s=$_GET['av_csa_s'];
	$cca_v_d=$_GET['cca_v_d'];
	$ica_v_d=$_GET['ica_v_d'];
	$eca_v_d=$_GET['eca_v_d'];
	$av_v_d=$_GET['av_v_d'];
	$cca_v_s=$_GET['cca_v_s'];
	$ica_v_s=$_GET['ica_v_s'];
	$eca_v_s=$_GET['eca_v_s'];
	$av_v_s=$_GET['av_v_s'];
	$j1_csa_d=$_GET['j1_csa_d'];
	$j2_csa_d=$_GET['j2_csa_d'];
	$j3_csa_d=$_GET['j3_csa_d'];
	$j1_v_d=$_GET['j1_v_d'];
	$j2_v_d=$_GET['j2_v_d'];
	$j3_v_d=$_GET['j3_v_d'];
	$j1_csa_s=$_GET['j1_csa_s'];
	$j2_csa_s=$_GET['j2_csa_s'];
	$j3_csa_s=$_GET['j3_csa_s'];
	$j1_v_s=$_GET['j1_v_s'];
	$j2_v_s=$_GET['j2_v_s'];
	$j3_v_s=$_GET['j3_v_s'];
	
	$j1_bloccoFlusso_d=isPresent(X_GET('j1_bloccoFlusso_d'));
	$j1_flussoBi_d=isPresent(X_GET('j1_flussoBi_d'));
	$j1_valvolaIpoMobile_d=isPresent(X_GET('j1_valvolaIpoMobile_d'));
	$j1_compressioni_d=isPresent(X_GET('j1_compressioni_d'));
	$j1_bloccoFlusso_s=isPresent(X_GET('j1_bloccoFlusso_s'));
	$j1_flussoBi_s=isPresent(X_GET('j1_flussoBi_s'));
	$j1_valvolaIpoMobile_s=isPresent(X_GET('j1_valvolaIpoMobile_s'));
	$j1_compressioni_s=isPresent(X_GET('j1_compressioni_s'));
	$j2_bloccoFlusso_d=isPresent(X_GET('j2_bloccoFlusso_d'));
	$j2_flussoBi_d=isPresent(X_GET('j2_flussoBi_d'));
	$j2_valvolaIpoMobile_d=isPresent(X_GET('j2_valvolaIpoMobile_d'));
	$j2_compressioni_d=isPresent(X_GET('j2_compressioni_d'));
	$j2_bloccoFlusso_s=isPresent(X_GET('j2_bloccoFlusso_s'));
	$j2_flussoBi_s=isPresent(X_GET('j2_flussoBi_s'));
	$j2_valvolaIpoMobile_s=isPresent(X_GET('j2_valvolaIpoMobile_s'));
	$j2_compressioni_s=isPresent(X_GET('j2_compressioni_s'));
	$j3_bloccoFlusso_d=isPresent(X_GET('j3_bloccoFlusso_d'));
	$j3_flussoBi_d=isPresent(X_GET('j3_flussoBi_d'));
	$j3_valvolaIpoMobile_d=isPresent(X_GET('j3_valvolaIpoMobile_d'));
	$j3_compressioni_d=isPresent(X_GET('j3_compressioni_d'));
	$j3_bloccoFlusso_s=isPresent(X_GET('j3_bloccoFlusso_s'));
	$j3_flussoBi_s=isPresent(X_GET('j3_flussoBi_s'));
	$j3_valvolaIpoMobile_s=isPresent(X_GET('j3_valvolaIpoMobile_s'));
	$j3_compressioni_s=isPresent(X_GET('j3_compressioni_s'));
	
	$query="INSERT INTO `us_report`( `studyInstanceUID`, `storia`, `quesito`, `cca_csa_d`, `ica_csa_d`, `eca_csa_d`,";
 	$query.="`av_csa_d`, `cca_v_d`, `ica_v_d`, `eca_v_d`, `av_v_d`, `j1_csa_d`, `j2_csa_d`, `j3_csa_d`, `j1_v_d`, `j2_v_d`, `j3_v_d`,"; 

	$query.="`j1_bloccoFlusso_d`, `j1_flussoBi_d`, `j1_valvolaIpoMobile_d`, `j1_compressioni_d`, `j2_bloccoFlusso_d`, `j2_flussoBi_d`, `j2_valvolaIpoMobile_d`, `j2_compressioni_d`, `j3_bloccoFlusso_d`, `j3_flussoBi_d`, `j3_valvolaIpoMobile_d`, `j3_compressioni_d`, `cca_csa_s`, `ica_csa_s`, `eca_csa_s`, `av_csa_s`, `cca_v_s`, `ica_v_s`, `eca_v_s`, `av_v_s`, `j1_csa_s`, `j2_csa_s`, `j3_csa_s`, `j1_v_s`, `j2_v_s`, `j3_v_s`, `j1_bloccoFlusso_s`, `j1_flussoBi_s`, `j1_valvolaIpoMobile_s`, `j1_compressioni_s`, `j2_bloccoFlusso_s`, `j2_flussoBi_s`, `j2_valvolaIpoMobile_s`, `j2_compressioni_s`, `j3_bloccoFlusso_s`, `j3_flussoBi_s`, `j3_valvolaIpoMobile_s`, `j3_compressioni_s`) VALUES (";

	$query.=$suid.",".$storia.",".$quesito.",".$cca_csa_d.",".$ica_csa_d.",".$eca_csa_d.",".$av_csa_d.",";

	$query.=$cca_v_d.",".$ica_v_d.",".$eca_v_d.",".$av_v_d.",".$j1_csa_d.",".$j2_csa_d.",".$j3_csa_d.",".$j1_v_d.",".$j2_v_d.",";$query.=$j3_v_d.",".$j1_bloccoFlusso_d.",".$j1_flussoBi_d.",".$j1_valvolaIpoMobile_d.",".$j1_compressioni_d.","; 
	$query.=$j2_bloccoFlusso_d.",".$j2_flussoBi_d.",".$j2_valvolaIpoMobile_d.",".$j2_compressioni_d.",".$j3_bloccoFlusso_d.",";
	$query.=$j3_flussoBi_d.",".$j3_valvolaIpoMobile_d.",".$j3_compressioni_d.",".$cca_csa_s.",".$ica_csa_s.",".$eca_csa_s.",";

	$query.=$av_csa_s.",".$cca_v_s.",".$ica_v_s.",".$eca_v_s.",".$av_v_s.",".$j1_csa_s.",".$j2_csa_s.",".$j3_csa_s.",".$j1_v_s.",";
	$query.=$j2_v_s.",".$j3_v_s.",".$j1_bloccoFlusso_s.",".$j1_flussoBi_s.",".$j1_valvolaIpoMobile_s.",".$j1_compressioni_s.",";

	$query.=$j2_bloccoFlusso_s.",".$j2_flussoBi_s.",".$j2_valvolaIpoMobile_s.",".$j2_compressioni_s.",";

	$query.=$j3_bloccoFlusso_s.",".$j3_flussoBi_s.",".$j3_valvolaIpoMobile_s.",".$j3_compressioni_s.")";


	//echo $query;
	$db->query($query);


}




function X_GET($par){
	if(array_key_exists(  $par ,  $_GET ))
	{
		return "Z";//$_GET[$par];
	}
	return "";
}

function isPresent($tag)
{
	//echo "Ghali".$tag=="";

	if($tag!="") return 1;
	return 0;
} 

function toSQLdate($ladate){
	$dd=substr($ladate, 0, 4)."-".substr($ladate, 4, 2)."-".substr($ladate, 6, 2);
	return $dd;
}
function toSQLtime($ladate){
	$dd=substr($ladate, 0, 2).":".substr($ladate, 2, 2).":".substr($ladate, 4, 2);
	return $dd;
}


function Redirect($url, $code = 302)
{
	
    if (strncmp('cli', PHP_SAPI, 3) !== 0)
    {
        if (headers_sent() !== true)
        {
            if (strlen(session_id()) > 0) // if using sessions
            {
                session_regenerate_id(true); // avoids session fixation attacks
                session_write_close(); // avoids having sessions lock other requests
            }

            if (strncmp('cgi', PHP_SAPI, 3) === 0)
            {
                header(sprintf('Status: %03u', $code), true, $code);
            }

            header('Location: ' . $url, true, (preg_match('~^30[1237]$~', $code) > 0) ? $code : 302);
        }

        exit();
    }

}


?>


