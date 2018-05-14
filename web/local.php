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
session_start();
/*FB*/
// this is my facebook configuration details
define('FACEBOOK_APP_ID', '763896243781229');
define('FACEBOOK_APP_SECRET', '4fc4103d5291ff31e553044ff867d5c0');
define('HTTP_HOST', 'http://www.isisinipazzi.it/');
define('STATE', md5(uniqid(rand(), TRUE)));
define('FACEBOOK_REDIRECT_URI', 'http://www.isisinipazzi.it/jvpAdmin/authorize.php');

function getLoginUrl() {
     $loginUrl = "https://www.facebook.com/dialog/oauth?"
          . "client_id=" . FACEBOOK_APP_ID
          . "&redirect_uri=" . FACEBOOK_REDIRECT_URI
          . "&state=" . STATE
          . "&response_type=code"
          . "&scope=user_about_me,email";
     return $loginUrl;
}
?>

<!DOCTYPE html>
<html>
<body>

<div style="height:35px;margin-top:0px;background-color:#ccd9ff;">
<div style="float:right">
<font size=3>
<?php if(!isset($_SESSION['name'])){?>
&nbsp;<a href=<?php echo getLoginUrl();?>>Login con Facebook</a>
 <?php
	}else{
 echo "Logged as: <b>".$_SESSION['name']."-".$_SESSION['email']."</b>&nbsp;<a href='http://www.isisinipazzi.it/jvpAdmin/logout.php'>logout</a>";
}
 ?>
</div>


 <div style="height:35px;width:200px;font-size:22;font-family:verdana;background-color:#ffffcc;"><a href='start.php'><img src=img/miniLogo.gif height=34></a>

</font>
 </div>
</div>
<center>
<img src=img/logo.gif>
<!--<div style="position:relative;height:220px;margin-top:50px;background-color:pink;">-->
 <!-- <div style="opacity:0.5;position:absolute;left:50px;top:-30px;width:300px;height:150px;background-color:#40B3DF"></div>-->
  <div class="w3-theme" style="opacity:0.3;position:absolute;left:120px;top:20px;width:100px;height:170px;"></div>
  <div style="margin-top:30px;width:360px;height:100px;padding:20px;border-radius:10px;border:10px solid #ccd9ff;font-size:80%;">
 <h1 style="font-family:verdana;">Jugular Venous Pulse Database<br>J-Pulse</h1>
 <!--<div style="letter-spacing:12px;font-size:15px;position:relative;left:25px;top:10px;">Data storing</div>
 <div style="color:#40B3DF;letter-spacing:12px;font-size:15px;position:relative;left:25px;top:20px;">Elaboration,
 <span style="background-color:#B4009E;color:#ffffff;">&nbsp;Plots</span></div>-->
 </div>
<!--</div>-->
</center>
<!--
<div id="gnu-banner">
 <a href="https://www.gnu.org/">
 <img src="img/gnu.png" size=50% alt=" [A GNU head] " /><strong>GNU</strong> Software System Information for VDC</a>
-->
<br><br>
<?php
if($_SESSION['auth']==true||true){
?>
<center>
	<a href=controller.php?action=start style="font-size:20px;font-family:verdana;" >Enter the site</a><br>
	
</center>
<?php
}
?>
<br><br><br><br><br><br><br><br><br><br><br><br>
<div  style="font-size:10px;font-family:verdana;">
Designed and Developed by <a href="controller.php?action=ceo">Francesco Sisini</a><br>
Credits <a href="controller.php?action=credits">The Team</a><br>
Technical   <a href="controller.php?action=specifiche">specification</a><br>
<a href="controller.php?action=disclaimer">Disclaimer</a>
</div>
</div><!-- /gnu-banner -->



</body>
</html>

