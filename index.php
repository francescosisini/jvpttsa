<?php
/*
Developer:  Ehtesham Mehmood
Site:       PHPCodify.com
Script:     Angularjs Login Script using PHP MySQL and Bootstrap
File:       index.php
*/



$a= $_GET['a'];
if(!isset($a)) $a="";
if($a!="b")
{
    echo "<script>window.location.href = '../index.php';</script>";
}


?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="noindex">
        <title>JVP Repository Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
    </head>
<body ng-app="AngularJSLogin" ng-controller="AngularLoginController as angCtrl">
    
        <div style="height:45px;width:100%;font-size:22;font-family:verdana;background-color:#000000;"><table style="width:100%;"><tr><td>&nbsp;</td><td style="text-align:left;color:white;"></td><td style="text-align:right;">&nbsp;nbsp;</td><td>&nbsp;</td></tr></table>
        </div>
    <div class="container">
      <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
          <div class="panel panel-info" >
                  <div class="panel-heading">
                      <div class="panel-title" style="text-align:center;">JVP Repository</div>
 
                  </div>
 
                  <div style="padding-top:30px" class="panel-body" >
                      <form name="login" ng-submit="angCtrl.loginForm()" class="form-horizontal" method="POST">
 
                          <div style="margin-bottom: 25px" class="input-group">
                                      <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                      <input type="text" id="inputemail" class="form-control" required autofocus ng-model="angCtrl.inputData.email">
                          </div>
 
                          <div style="margin-bottom: 25px" class="input-group">
                                      <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                      <input type="password" id="inputpassword" class="form-control" required ng-model="angCtrl.inputData.password">
                          </div>
                          <div class="form-group">
                              <!-- Button -->
                              <div class="col-sm-12 controls">
                                  <button type="submit" class="btn btn-primary pull-left"><i class="glyphicon glyphicon-log-in"></i> Log in</button>
                              </div>
                          </div>
                              <div class="alert alert-danger" ng-show="errorMsg">
                                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                      ×</button>
                                  <span class="glyphicon glyphicon-hand-right"></span>&nbsp;&nbsp;{{errorMsg}}
                              </div>
                          </form>
                      </div>
                  </div>
      </div>
  </div>
<script>
	angular.module('AngularJSLogin', [])
	.controller('AngularLoginController', ['$scope', '$http', function($scope, $http) {
		this.loginForm = function() {
 
			var user_data='user_email=' +this.inputData.email+'&user_password='+this.inputData.password;
 
			$http({
				method: 'POST',
				url: 'login.php',
				data: user_data,
				headers: {'Content-Type': 'application/x-www-form-urlencoded'}
			})
			.success(function(data) {
        console.log(data);
				if ( data.trim() === 'correct') {
					window.location.href = 'main.php';
				} else {
					$scope.errorMsg = "Invalid Email and Password";
				}
			})
		}
 
	}]);
	</script>
 <div style="text-align:center;">TEKAMED-DAA&nbsp;&copy;2018 <a href="http://tekamed.it">Tekamed</a> </div>
    </body>
</html>
