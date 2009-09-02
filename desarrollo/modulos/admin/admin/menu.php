<?php
#OOB/N1 Framework [2008 - Nutus] - PM

// menu JSON para EXT-JS
//error_reporting(0);
global $ari;
$ari->popup = 1;  // no mostrar el main_frame 

$menu =  oob_module::adminFullMenu();
$json_menu = array();

foreach ($menu as $datos_padre)
{
	$padre = array();
	$padre['cls'] = 'Father-node';
	$padre['leaf'] = false;
	$padre['text'] = $datos_padre['name'];
	$padre['id'] = $datos_padre['id'];
	$padre['children'] = array();

	if (isset($datos_padre['menu']) && is_array ($datos_padre['menu']))
	{
		foreach ($datos_padre['menu'] as $datos_hijo)
		{
			$hijo = array();
			$hijo['iconCls'] = 'ChildNode';
			$hijo['leaf'] = true;
			$hijo['text'] = $datos_hijo['name'];
			$hijo['id'] = array($datos_hijo['name'], $ari->adminaddress . '/' . $datos_hijo['link']); // nombre del tab, url
			$padre['children'][] = $hijo;
		}
	}
	
	$json_menu[] = $padre;
}



//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $json_menu );
$obj_comunication->send(true,true);

?>