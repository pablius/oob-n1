<?php

//codigo por jpcoseani
//script que devuelve el listado de modulos

global $ari;
$ari->popup = 1;  // no mostrar el main_frame 


if ( isset($_POST['UpdateList']) ){
	$module_object = new OOB_module(); 
	$module_object->updateModulesList ();	
	$ari->clearCache();
}	

//CAMBIO DE ESTADO DE LOS MODULOS ( HABILITADO O NO)
if ( isset($_POST['UpdateEnabledData']) ){

$change_status = json_decode( $_POST['UpdateEnabledData'], true );

foreach( $change_status as $chk )
 	{
		$module = new OOB_module( $chk['modulename'] );
		if( $chk['checked'] == "true" ){		
			$module->enable();
		}else{
			$module->disable();
		}		
  	}
}

$i = 0;
$return = array();

//SE OBTIENE EL LISTADO DE MODULOS
if ( $modules = OOB_module::listModules('all', true, 'nicename' ) ){
	
	foreach ($modules as $m)
	{
		$return[$i]['nicename'] = $m->nicename();
		$return[$i]['modulename'] = $m->name();
		$return[$i]['description'] = $m->description();
		$return[$i]['checked'] = $m->isenabled();
		$return[$i]['optional'] = $m->optional();
	$i++;
	}
}

$result = array();
$result["totalCount"] = $i;
$result["topics"] = $return;		

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $result );
$obj_comunication->send(true,true);

?>