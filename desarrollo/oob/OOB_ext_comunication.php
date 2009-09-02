<?php

class OOB_ext_comunication{

	
private $message, $code, $data;

public function __construct ($tags = null)
	{		
		$this->message = "";
		$this->code = "200";
		$this->data = "";
	}


public function set_message( $value ){

	$this->message = $value;

}//end function

public function set_code( $value ){

	$this->code = $value;

}//end function

public function set_data( $value ){

	$this->data = $value;

}//end function

public function send( $print = false , $json = false ){	
		
	$result = $this->data ;		
	if( $json ){
		$result = json_encode($this->data);
	}

	if($print){
		echo $result;
	}else{
		return $result;
	}
}

}//end class

?>


