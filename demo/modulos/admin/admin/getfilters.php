<?php

global $ari;
$ari->popup = 1;

if( !isset($_POST['tabid']) ){
	throw new OOB_Exception_400("La variable [tabid] no esta definida");
}

$tab_id = $_POST['tabid'];

$cache_tab = new admin_session_state();
echo $cache_tab->get_cache_filters( $tab_id );

?>