<?php
define('FACEBOOK_APP_ID', '763896243781229');
define('FACEBOOK_APP_SECRET', '4fc4103d5291ff31e553044ff867d5c0');
define('HTTP_HOST', 'http://www.isisinipazzi.it/');
//define('STATE', md5(uniqid(rand(), TRUE)));
define('FACEBOOK_REDIRECT_URI', 'http://www.isisinipazzi.it/jvpAdmin/authorize.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);
@include "config.php";
session_start(); 
$facebook_code = $_GET['code'];
//echo $facebook_code;
$access_token = getAccessToken($facebook_code);
$user = getUserInfo($access_token);
$_SESSION['name']=$user->name;
$_SESSION['FBID']=$user->id;
$_SESSION['email']=$user->email;
$_SESSION['auth']=true;
//traceAccess($user->id,$user->name."-".$user->email);
echo "SESSION ".$_SESSION['name'];
header("Location: index.php");



function getUserInfo($access_token) {
	echo "<br><br> acce_t=".$access_token;
	$graph_url = "https://graph.facebook.com/me?access_token=".$access_token;
	//$graph_url = "https://graph.facebook.com/me?access_token="."EAAK2wnRBfm0BAMPbgBDhHlpH3bYsKwtTXlZBzzpOkZBJlr2JHXdLgZCeSwblTvISiZCr66IJZCbsL5oUzvBdztKZC7WWiMWvLIuD9BMz7ZBFOYTZALLhIFIjdQg4PZCBRUKviM2lou7n4ZATBh1QZCZAXaMg104BcHoa1LMI6opsuzhmYwZDZD";
    
	 $user = json_decode(curl($graph_url));
     if($user != null && isset($user->name)) {
          return $user;
     }
     return FALSE;
}



function curl($url) {
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
     $data = curl_exec($ch);
     curl_close($ch);
     return $data;
}
function getAccessToken($facebook_code) {
     $token_url = "https://graph.facebook.com/oauth/access_token?"
          . "client_id=" . FACEBOOK_APP_ID
          . "&redirect_uri=" . urlencode(FACEBOOK_REDIRECT_URI)
          . "&client_secret=" . FACEBOOK_APP_SECRET
          . "&code=" . $facebook_code;
	//echo "URL: ".$token_url;
     $response = curl($token_url);

	var_dump($response);
	//$response è una stringa
	$s1=explode(",",$response);
	var_dump($s1);
	$s2=explode(":",$s1[0]);
	echo "S2<br>";
	var_dump($s2);
	$s3=str_replace('"', '', $s2[1]);
	echo "S3:".$s3;
     //$params = null;
     //$params=explode(":",$response);
	//parse_str($response['access_token'], $params);
	return $s3;

}

function traceAccess($fbid,$name){
	$sql = "SELECT * from login  where fbid='$fbid' ";
	//echo $sql;
	$query = @mysql_query($sql) or die (mysql_error());
	if(mysql_num_rows($query) > 0){
		$sql = "update  login set visit=visit+1 where fbid='$fbid' ";
		$query = @mysql_query($sql);
	}else{
		$sql = "insert into  login (fbid,name,visit) values ('$fbid','$name','1')";
		$query = @mysql_query($sql);
	}

	return true;
}



?>
