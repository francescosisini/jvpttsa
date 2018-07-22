<?php
# PHPlot Example: Simple line graph
require_once 'phplot.php';
require_once 'mylibrary.php';
require_once 'myPlotlibrary.php';
require_once 'db.php';
require_once 'data.php';

$db = new Db(); 
$dataObj=new Data();


$suid=$_GET['study'];
$query="SELECT * from us_report inner join us_study on us_report.studyInstanceUID=us_report.studyInstanceUID where us_report.studyInstanceUID =".$db->quote($suid);

$query="SELECT * from  us_study  where us_study.studyInstanceUID =".$db->quote($suid);

$rows = $db -> select($query);
$isCCSVIreport=count($rows,COUNT_NORMAL);

/*Dati InFlow*/
$infl=getInFlow($suid);
$hbinf=$infl["hbinf"];
$cbf=$infl["cbf"];

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
     "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <style>
         
         th {
             
             text-align:left;
         }
         .mtd
         {

             text-align:right;
             }
         
        </style>
<title>JVP and Flow Study</title>
</head>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
<body style="width:800px">
    


<h1 style="text-align: center; font-size:24px; font-family: Times New Roman, Georgia, Serif;">
Centro Malattie Vascolari -Universit&agrave; degli studi di Ferrara-
</h1>
<h2 style="text-align: center; font-size:18px; font-family: Times New Roman, Georgia, Serif;">ANALISI ULTRASONORA NON INVASIVA DEL RITORNO VENOSO CEREBRALE E DEL POLSO GIUGULARE</h1>
<br><br>

<table >
    <tr>
        <th >Nome e cognome</th><td class=mtd><?php print($rows[0]['patientName']);?></td>
    </tr>
    <tr>
        <th>Sesso</th>
        <td class=mtd>
            <?php
            if ($rows[0]['sex']=="")
            {
            ?>
                <div ng-app="myApp" ng-controller="myCtrl">
                    
                    <li>{{sex}} </li>
                    
                    <form>
                        <span style="color:red;">Sesso</span><input type="text" ng-model="bsex" />
                        <input type="hidden" ng-model="bsid" />
                       
                        
                        <input type="button" value="Submit" ng-click="insertData()" />
                    </form>
                </div>
                <script>
                 var app = angular.module('myApp',[]);
                 app.controller('myCtrl',function($scope,$http){
                     $scope.bsid = "<?php echo $suid ?>";
                     
                     $scope.insertData=function(){      
                         $http.get("controller.php?action=updatesex&sex="+$scope.bsex+"&sid="+$scope.bsid
                         ).then(function(response){
                             $scope.sex = $scope.bsex;
                             console.log("Data Inserted Successfully");
                         },function(error){
                             alert("Sorry! Data Couldn't be inserted!");
                             $scope.sex = "";
                             console.error(error);
                         });
                     }
                 });
                </script>
            <?php
            }
            else
            {
            ?>
                <?php echo $rows[0]['sex'];?>
<?php
}
?>


        </td>
    </tr>
    <tr>
        <th>Codice Fiscale</th>
        <td class=mtd>
            <?php
            if ($rows[0]['primarycode']=="")
            {
            ?>
                <div ng-app="myApp3" ng-controller="myCtrl3">
                    
                    <li>{{cf}} </li>
                    
                    <form>
                        <span style="color:red;">Codice Fiscale</span><input type="text" ng-model="bcf" />
                        <input type="hidden" ng-model="bsid" />
                       
                        
                        <input type="button" value="Submit" ng-click="insertData()" />
                    </form>
                </div>
                <script>
                 var app = angular.module('myApp3',[]);
                 app.controller('myCtrl3',function($scope,$http){
                     $scope.bsid = "<?php echo $suid ?>";
                     
                     $scope.insertData=function(){      
                         $http.get("controller.php?action=updatecf&cf="+$scope.bcf+"&sid="+$scope.bsid
                         ).then(function(response){
                             $scope.cf = $scope.bcf;
                             console.log("Data Inserted Successfully");
                         },function(error){
                             alert("Sorry! Data Couldn't be inserted!");
                             $scope.bcf = "";
                             console.error(error);
                         });
                     }
                 });
                </script>
            <?php
            }
            else
            {
            ?>
                <?php echo $rows[0]['primarycode'];?>
<?php
}
?>

        </td>
    </tr>
    <tr>
        <th>Indirizzo di residenza</th>
            <td class=mtd>
            <?php
            if ($rows[0]['address']=="")
            {
            ?>
                <div ng-app="myApp2" ng-controller="myCtrl2">
                    
                    <li>{{add}} </li>
                    
                    <form>
                        <span style="color:red;">Indirizzo</span><input type="text" ng-model="badd" />
                        <input type="hidden" ng-model="bsid" />
                       
                        
                        <input type="button" value="Submit" ng-click="insertData()" />
                    </form>
                </div>
                <script>
                 var app = angular.module('myApp2',[]);
                 app.controller('myCtrl2',function($scope,$http){
                     $scope.bsid = "<?php echo $suid ?>";
                     
                     $scope.insertData=function(){      
                         $http.get("controller.php?action=updateadd&add="+$scope.badd+"&sid="+$scope.bsid
                         ).then(function(response){
                             $scope.add = $scope.badd;
                             console.log("Data Inserted Successfully");
                         },function(error){
                             alert("Sorry! Data Couldn't be inserted!");
                             $scope.badd = "";
                             console.error(error);
                         });
                     }
                 });
                </script>
            <?php
            }
            else
            {
            ?>
                <?php echo $rows[0]['address'];?>
<?php
}
?>

        </td>
    </tr>
</table>




<?php


for($ik=1;$ik<=2;$ik++)
{
    $iscvp=false;
    $isjvp=false;
    if($ik==1)
    {
	$rl="R";
	$lato="DESTRA";
    }
    else
    {
	$rl="L";
	$lato="SINISTRA";
    }
    $jp=2;
    
    /*Selezione del video in base allo StudyID e al lato DESTRO o SINISTRO*/
    $vid=getVideoId($suid,$rl,$jp);
    if($vid>=0)
    {
        /*** PLOT JVP***/
        //Calcolo Valori limiti degli assi per il plot del JVP
        $limit=getTraceParameter($vid);
        $max=$limit["max"];
        $min=$limit["min"];
        $avg=$limit["avg"];
        $std=$limit["std"];
        $delta=$max-$min;
        $tmax=$limit["tmax"];
        $dataObj->CSA_mean=$avg;
        $dataObj->CSA_min=$min;
        $dataObj->CSA_max=$max;
        
        
        //Fattore di conversione pixel->cm^2 e frame->s
        $cal=getCalibration($vid);
        $calibration=$cal['calibration'];
        $tc=$cal['tc']; 
        
        
        
        //Calcolo media e std Wave 'a'
        $stat=getUncalibratedJVPstats($vid,"a");
        $dataObj->a_m=round( $stat['m']*$calibration,2);
        $dataObj->a_sd=round( $stat['d']*$calibration,2);
        
        
        //Calcolo media e std Wave 'c'
        $stat=getUncalibratedJVPstats($vid,"c");
        $dataObj->c_m=round( $stat['m']*$calibration,2);
        $dataObj->c_sd=round( $stat['d']*$calibration,2);
        
        
        //Calcolo media e std Wave 'x'
        $stat=getUncalibratedJVPstats($vid,"x");
        $dataObj->x_m=round( $stat['m']*$calibration,2);
        $dataObj->x_sd=round( $stat['d']*$calibration,2);
        
        
        //Calcolo media e std Wave 'v'
        $stat=getUncalibratedJVPstats($vid,"v");
        $dataObj->v_m=round( $stat['m']*$calibration,2);
        $dataObj->v_sd=round( $stat['d']*$calibration,2);
        
        
        //Calcolo media e std Wave 'y'
        $stat=getUncalibratedJVPstats($vid,"y");
        $dataObj->y_m=round( $stat['m']*$calibration,2);
        $dataObj->y_sd=round( $stat['d']*$calibration,2);
        
        
        
        //Mostra il Doppler campionato
        $isDoppler=false;
        $did= getDopplerId($suid,$rl,$jp);
        if($did>=0)
        {
            $dplot=plotDoppler($did);
            if($dplot!=null) $isDoppler=true;
        }
        
        //Calcola il flusso
        $isFlow=false;
        $flow=getCalculatedFlowData($suid,$rl,$jp,1,1);
        if($flow!=null)
        {
            echo "Diagramma temporale del flusso volumetrico di sangue<br>";
 	    $isFlow=true;
	    $fplot=plotFLow($flow);
        }
        
        
        
        /***	Delta t tra le onde JVP	***/ 
        
        $ja=getUSSelectiveJVPwaves('a',$vid);
        $jx=getUSSelectiveJVPwaves('x',$vid);
        $i=0;
        if(!empty($ja)&&!empty($jx)){
	    while($jx[$i]['number']<$ja[0]['number'])$i++;
	    $rn = count($jx,COUNT_NORMAL)-$i;
	    $d=$i;
	    $dtax=0;
	    $inx=0;
	    if($rn>0){
		for ($i=0; $i<$rn-1; $i++) {
		    $si=$ja[$i]['number'];
		    $sf=$ja[$i+1]['number'];
		    $periodo=$sf+1-$si;
		    $dtax=($jx[$i+$d]['number']-$ja[$i]['number'])/$periodo;
		    if($dtax<1){
			$tax[$inx]['value']=$dtax;
			$inx++;
		    }
		}
		if(!empty($tax)){
		    $stat=stats($tax);
		    $mudtax=round($stat[0]['mu'],3);
		    $sddtax=round($stat[0]['sd'],3);
		    $dataObj->dxa_m=$mudtax;
		    $dataObj->dxa_sd=$sddtax;
		}
	    }
	    
        }
        
        $ja=getUSSelectiveJVPwaves('v',$vid);
        $jx=getUSSelectiveJVPwaves('y',$vid);
        $i=0;
        if(!empty($ja)&&!empty($jx)){
	    while($jx[$i]['number']<$ja[0]['number'])$i++;
	    $rn = count($jx,COUNT_NORMAL)-$i;
	    $d=$i;
	    $dtax=0;
	    $inx=0;
	    for ($i=0; $i<$rn-1; $i++) {
		$si=$ja[$i]['number'];
		$sf=$ja[$i+1]['number'];
		$periodo=$sf+1-$si;
		$dtax=($jx[$i+$d]['number']-$ja[$i]['number'])/$periodo;
		if($dtax<1){
		    $tax2[$inx]['value']=$dtax;
		    $inx++;
		}
	        
	    }
	    $stat=stats($tax2);
	    $mudtvy=round($stat[0]['mu'],3);
	    $sddtvy=round($stat[0]['sd'],3);
	
	    $dataObj->dvy_m=$mudtvy;
	    $dataObj->dvy_sd=$sddtvy;
	    
            
	    
        }
        
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
        
        $ers=getUSSelectiveECGwaves("P",$vid);
        $rn = count($ers,COUNT_NORMAL);
        
        for ($i=0; $i<$rn-1; $i++) {
	    $si=$ers[$i]['number'];
	    $sf=$ers[$i+1]['number'];
	    $periodo=$sf+1-$si;
	    $queryJ="SELECT *  FROM  us_jvp  WHERE number>=".$si." and number<".$sf." and us_jvp.videoclipid =".$vid;
	    $jrs = $db-> select($queryJ);
	    $en = count($jrs,COUNT_NORMAL);
	    for ($j=0; $j<$en; $j++) {
		$isjvp=true;
		$wave=$jrs[$j]['acxvyWave'];
		$jn=$jrs[$j]['number'];
		if($wave=="a"){
			$a_ap[$na]['value']=($jn-$si)/$periodo;
		    $na+=1;
		    $dap+=($jn-$si)/$periodo;
		    
		}
		if($wave=="x"){
		    $a_xp[$nx]['value']=($jn-$si)/$periodo;
		    $nx+=1;
		    $dxp+=($jn-$si)/$periodo;
		    
		}
	    }
        }
        if(!empty($a_ap)){
	    $stat=stats($a_ap);
	    $dataObj->dajp_m=round($stat[0]['mu'],3);
	    $dataObj->dajp_sd=round($stat[0]['sd'],3);
        }
        if(!empty($a_xp)){
	    $stat=stats($a_xp);
	    $dataObj->dxjp_m=round($stat[0]['mu'],3);
	    $dataObj->dxjp_sd=round($stat[0]['sd'],3);
        }
        
        
        //Realzione onda T con onda v
        $dvt=0;
        $nv=0;
        $periodo=0;
        $yt=0;
        $ny=0;
        $dyt=0;
        
        $ers=getUSSelectiveECGwaves("T",$vid);
$rn = count($ers,COUNT_NORMAL);
        
        for ($i=0; $i<$rn-1; $i++) {
	    $si=$ers[$i]['number'];
	    $sf=$ers[$i+1]['number'];
	    $periodo=$sf+1-$si;
	    $queryJ="SELECT *  FROM  us_jvp  WHERE number>=".$si." and number<".$sf." and us_jvp.videoclipid =".$vid;
	    $jrs = $db-> select($queryJ);
	    $en = count($jrs,COUNT_NORMAL);
	    for ($j=0; $j<$en; $j++) {
		$isjvp=true;
		$wave=$jrs[$j]['acxvyWave'];
		$jn=$jrs[$j]['number'];
		//echo $wave;
		if($wave=="v"){
		    $a_vt[$nv]['value']=($jn-$si)/$periodo;
		    $nv+=1;
		}
		if($wave=="y"){
			$a_yt[$ny]['value']=($jn-$si)/$periodo;
			$ny+=1;
		}
	}
}
if(!empty($a_vt)){
	$stat=stats($a_vt);
	$dataObj->dvjt_m=round($stat[0]['mu'],3);
	$dataObj->dvjt_sd=round($stat[0]['sd'],3);
}

if(!empty($a_yt)){
	$stat=stats($a_yt);
	$dataObj->dyjt_m=round($stat[0]['mu'],3);
	$dataObj->dyjt_sd=round($stat[0]['sd'],3);
}

//Calcolo Ritardo tra JVP/ECG per Cruenta
$c_dap=0;
$c_na=0;
$c_periodo=0;
$c_xp=0;
$c_dxp=0;
$c_nx=0;

//ID per lo screenshot
$sid=getScreenshotId(getCVPid($suid));

$ers=getCVPSelectiveECGwaves("P",$sid);
$rn = count($ers,COUNT_NORMAL);

for ($i=0; $i<$rn-1; $i++) {
	$si=$ers[$i]['number'];
	$sf=$ers[$i+1]['number'];
	$c_periodo=$sf+1-$si;
	$queryJ="SELECT *  FROM  cvp_waves  WHERE number>=".$si." and number<".$sf." and cvp_waves.idscreenshot =".$sid;
	//echo "$queryJ<br>";
	$jrs = $db-> select($queryJ);
	$en = count($jrs,COUNT_NORMAL);
	for ($j=0; $j<$en; $j++) {
		$iscvp=true;
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
if($iscvp){
	
//Calcolo media e std Wave 'a'
$stat=getCalibratedCVPstats("a",$sid);
$dataObj->cvp_a_m=round( $stat['m'],2);
$dataObj->cvp_a_sd=round( $stat['d'],2);


//Calcolo media e std Wave 'c'
$stat=getCalibratedCVPstats("c",$sid);
$dataObj->cvp_c_m=round( $stat['m'],2);
$dataObj->cvp_c_sd=round( $stat['d'],2);


//Calcolo media e std Wave 'x'
$stat=getCalibratedCVPstats("x",$sid);
$dataObj->cvp_x_m=round( $stat['m'],2);
$dataObj->cvp_x_sd=round( $stat['d'],2);


//Calcolo media e std Wave 'v'
$stat=getCalibratedCVPstats("v",$sid);
$dataObj->cvp_v_m=round( $stat['m'],2);
$dataObj->cvp_v_sd=round( $stat['d'],2);


//Calcolo media e std Wave 'y'
$stat=getCalibratedCVPstats("y",$sid);
$dataObj->cvp_y_m=round( $stat['m'],2);
$dataObj->cvp_y_sd=round( $stat['d'],2);


	$c_dap=($c_dap/$c_na);
	$c_dxp=($c_dxp/$c_nx);
	
	
	$dataObj->dacp_m=$c_dap;
	$dataObj->dxcp_m=$c_dxp;
}


    //Preparazione dati per plot ECG
    
    $myecg=getECGplottableData($vid);
    $data=getCalibratedJVP($vid);
    $plot=plotCSA($data,$min,$delta,$tmax,$max);
    $plot=plotCSAwithECG($data,$myecg,$min,$delta,$tmax,$max);
?>
<br><br>
 <h3 style="text-align: center; font-size:16px; font-family: Times New Roman, Georgia, Serif;"><?php print("Giugulare  $lato");?></h3> 
<hr>
<?php
if(!empty($plot)){ 
    $da=round($dataObj->getDa(),3);
    $dv=round($dataObj->getDv(),3);
    $daa=round($dataObj->getDaOna(),3);
    $dvv=round($dataObj->getDvOnv(),3);
?>
    <b>Diagramma temporale della IJV CSA</b>
    <img src="<?php echo $plot->EncodeImage();?>" alt="Plot Image">    
<?php

}
?>
<!--Tutti i parametri della CSA-->
<br><br>
<b>Parametri misurati</b><br>
<table>
    <tr>
        <td>
            <!--Paramteri CSA-->
            <table id="csa" >
                <tr>
                    <td>CSA<sub>media</sub></td><?php echo "<td>$dataObj->CSA_mean</td>";?><td>cm<sup>2</sup></td>
                </tr>
                <tr>
                    <td>CSA<sub>min</sub></td><?php echo "<td>$dataObj->CSA_min</td>";?><td>cm<sup>2</sup></td>
                </tr>
                <tr>
                    <td>CSA<sub>max</sub></td><?php echo "<td>$dataObj->CSA_max</td>";?><td>cm<sup>2</sup></td>
                </tr>
                    
            </table>
        </td>
        <td>
            <?php
            if($isjvp)
            {?>
            <!--Analisi JVP-->
            <table id=jvp>
                <tr>
                    <td>a</td><td>c</td><td>x</td><td>v</td><td>y</td><td>&Delta;a</td><td>&Delta;v</td><td>&Delta;a/a</td><td>&Delta;v/v</td><td>&Delta;xa</td><td>&Delta;vy</td>
                </tr>
                <tr>
                    <td colspan=2 align=center>#</td>
                </tr>
                <tr><?php
                    echo "<td>$dataObj->a_m</td><td>$dataObj->c_m</td><td>$dataObj->x_m</td><td>$dataObj->v_m</td><td>$dataObj->y_m</td><td>$da</td><td>$dv</td><td>$daa</td><td>$dvv</td><td>$dataObj->dxa_m</td><td>$dataObj->dvy_m</td>";
                    ?>
                </tr>
            </table>
            
        
        <?php }?>
        </td>
        <td>
            <?php
            if($isjvp)
            {?>
            <!-- Analisi ECG-->
            <table id=ecg>
                <tr>
                    <td>&Delta;aP</td><td>&Delta;vT</td><td>&Delta;xP</td><td>&Delta;yT</td>
                </tr>
                
                <tr>
                    <td colspan=6 align=center> (ccf)</td>
                </tr>
                <tr>
                    <?php
                    echo "<td>&nbsp;$dataObj->dajp_m</td><td>$dataObj->dvjt_m</td><td>$dataObj->dxjp_m</td><td>$dataObj->dyjt_m</td>";
                    ?>
                </tr>
                
            </table>
            <?php }?>
        </td>
    </tr>
    </table>
                

<?php
if($isDoppler){ ?>
<img src="<?php echo $dplot->EncodeImage();?>" alt="Plot Image">
<?php 
}
?>
<?php
if($isFlow){ ?>
Tracciato temporale del flusso volumetrico<br>
<img src="<?php echo $fplot->EncodeImage();?>" alt="Plot Image">
<?php 
}
?>
<?php

if($iscvp&&$isjvp)
{
?>
CVP Parameters<br>
<table style="font-family:verdana;font-size:14px" spacing=1 border=1 >
<tr>
<td>a</td><td>c</td><td>x</td><td>v</td><td>y</td><td>&Delta;a</td><td>&Delta;v</td><td>&Delta;a/a</td><td>&Delta;v/v</td>
<tr>
	<td colspan=7 align=center>(mmHg)</td><td colspan=2 align=center>#</td>
</tr>
</tr>
<tr>
<?php
$da=round($dataObj->getcvp_Da(),3);
$dv=round($dataObj->getcvp_Dv(),3);
$daa=round($dataObj->getcvp_DaOna(),3);
$dvv=round($dataObj->getcvp_DvOnv(),3);
echo "<td>$dataObj->cvp_a_m</td><td>$dataObj->cvp_c_m</td><td>$dataObj->cvp_x_m</td><td>$dataObj->cvp_v_m</td><td>$dataObj->cvp_y_m</td><td>$da</td><td>$dv</td><td>$daa</td><td>$dvv</td>";

?>
</tr>
</table>




<br>
<b>Pressure vs ECG wave relationship</b><br><br>
CVP/ECG:
<?php
echo "&Delta;t<sub> a<sub>cvp</sub>P</sub>=".round($dataObj->dacp_m,3)."&nbsp;&Delta;t <sub>x<sub>cvp</sub>P</sub>=".round($dataObj->dacp_sd,3)." (Cardiac cycle fraction)" ;
?>
</p>
<p>JVP/ECG:
<?php
echo "&Delta;t<sub> a<sub>jvp</sub>P</sub>=".round($dataObj->dajp_m,3)."&nbsp;&Delta;t <sub>x<sub>jvp</sub>P</sub>=".round($dataObj->dajp_sd,3) ;
?>
</p>
<p>CVP/JVP
<?php
$daa=$dataObj->getDaa();
$dxx=$dataObj->getDxx();
echo "&Delta;t<sub>a<sub>jvp</sub>a<sub>cvp</sub></sub>=".round(($daa),3)."&nbsp;&Delta;t<sub>x<sub>jvp</sub>x<sub>cvp</sub></sub>=".round(($dxx),3)." (Cardiac cycle fraction)" ;
?>
</p>
<?php
}
?>
<br>

<?php

}
}

?>
<br>
<p style="text-align:left;">
<b>Data ____________</b>
</p>
<p style="text-align:right;">
    <b>Il Responsabile Dott.________________________</b><br><br>
    <b>Firma ________________________</b>
</p>
</body>
</html>

