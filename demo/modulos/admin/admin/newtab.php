<?php
global $ari;
$ari->popup = 1;

if( !isset($_POST['url']) ){
	throw new OOB_Exception_400("La variable [url] no esta definida");
}

if( !isset($_POST['title']) ){
	throw new OOB_Exception_400("La variable [title] no esta definida");
}

if( !isset($_POST['params']) ){
	throw new OOB_Exception_400("La variable [params] no esta definida");
}

$url = $_POST['url'];
$title = $_POST['title'];
$params = $_POST['params'];
if( $tab_cache = new admin_session_state() ){
	if( $tab_id = $tab_cache->add_tab_cache( $url , $title , $params ) ){	
			
		//RESULTADO
		$obj_comunication = new OOB_ext_comunication();
		$obj_comunication->set_data( array( 'id' => $tab_id ) );
		$obj_comunication->send(true,true);
		
	}
	else
	{
		echo false;
	}
}	

?>
