<?php
global $ari;
$ari->popup = 1;

if( !isset( $_POST['tab_id'] ) ){
	throw new OOB_Exception_400("La variable [tab_id] no esta definida");
}

$tab_id = $_POST['tab_id'];

if( $tab_cache = new admin_session_state() ){
	if( $tab_cache->clear_tab_cache( $tab_id ) ){	
		echo true;
	}
	else
	{
		echo false;
	}
}	

?>