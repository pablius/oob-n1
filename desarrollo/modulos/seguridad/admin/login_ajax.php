<?php

global $ari;
$ari->popup = 1;


$resultado = array();
$resultado["errors"] = array();
$resultado["success"] = false;	

if( !isset($_POST['username']) ){
	throw new OOB_Exception_400("La variable [username] no esta definida");
}	

if( !isset($_POST['password']) ){
	throw new OOB_Exception_400("La variable [password] no esta definida");
}	
	
	
	
		
		if (oob_user::login ($_POST['username'], $_POST['password']))
		{	
			$resultado["success"] = true;
			
		}
		else
		{
			$resultado["success"] = false;
		}
		
//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true,true);	


?>
