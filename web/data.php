<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);



	class Data {
	
	/***Parametri CSA***/
	public $dxa_m="";
	public $dxa_sd="";
	public $dvy_m="";
	public $dvy_sd="";
	public $CSA_mean="";
	public $CSA_max="";
	public $CSA_min="";
	public $a_m="";
	public $a_sd="";
	public $c_m="";
	public $c_sd="";
	public $x_m="";
	public $x_sd="";
	public $v_m="";
	public $v_sd="";
	public $y_m="";
	public $y_sd="";

	
public function getDa(){
	return ($this->a_m-$this->x_m);
	}
public function getDaOna(){
	if($this->a_m!=0)
		return ($this->a_m-$this->x_m)/$this->a_m;
	else
		return "";
	}
public function getDv(){
	return ($this->v_m-$this->y_m);
	}
public function getDvOnv(){
	if($this->v_m!=0)
		return ($this->v_m-$this->y_m)/$this->v_m;
	else
		return "";
	}


	/***Parametri CVP***/
	public $cvp_a_m="";
	public $cvp_a_sd="";
	public $cvp_c_m="";
	public $cvp_c_sd="";
	public $cvp_x_m="";
	public $cvp_x_sd="";
	public $cvp_v_m="";
	public $cvp_v_sd="";
	public $cvp_y_m="";
	public $cvp_y_sd="";

	
public function getcvp_Da(){
	return ($this->cvp_a_m-$this->cvp_x_m);
	}
public function getcvp_DaOna(){
	if($this->a_m!=0)
		return ($this->cvp_a_m-$this->cvp_x_m)/$this->cvp_a_m;
	else
		return "";
	}
public function getcvp_Dv(){
	return ($this->cvp_v_m-$this->cvp_y_m);
	}
public function getcvp_DvOnv(){
	if($this->cvp_v_m!=0)
		return ($this->cvp_v_m-$this->cvp_y_m)/$this->cvp_v_m;
	else
		return "";
	}



	/***Paramatri CSA/ECG***/
	public $dajp_m="";
	public $dajp_sd="";
	public $dxjp_m="";
	public $dxjp_sd="";
	public $dvjt_m="";
	public $dvjt_sd="";
	public $dyjt_m="";
	public $dyjt_sd="";
	/***Paramatri CSA/CVP***/
	public $dacp_m="";
	public $dacp_sd="";
	public $dxcp_m="";
	public $dxcp_sd="";
public function getDaa(){
	return ($this->dajp_m-$this->dacp_m);
	}
public function getDxx(){
	return ($this->dxjp_m-$this->dxcp_m);
	}
    }

?>
