<?php
#OOB/N1 Framework [�2004,2005 - Nutus]
/*
 * Created on 22-jun-2005
 * @author Victor Stornati (victor.stornati@nutus.com.ar)
 */

if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('perspectives','config','admin')) )
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
}
 
// get perspectives
global $ari;
$handle = $ari->url->getVars();

$ari->t->caching = 0; // dynamic content 

//// check the delete selector, and delete if selected
if (isset ($_POST['delete_submit']) && isset($_POST['selected_perspectives']))
{
	foreach($_POST['selected_perspectives'] as $name_perspective)
	{
//		$perspective = new seguridad_perspective($name_perspective);
//		$perspective->delete();		
	}
}
 	
// finally get the data
$return = array();
if ($perspectivas = oob_perspective::listPerspectives()) {
	// show time
	$i = 0;
	foreach ($perspectivas as $p)
	{
		$return[$i]['name']= $p;
		$return[$i]['path']= $ari->get('filesdir') . DIRECTORY_SEPARATOR . 'perspectives' . DIRECTORY_SEPARATOR . $p ;
		$return[$i]['path2'] = str_replace(DIRECTORY_SEPARATOR,"/",$return[$i]['path']);
		++$i;
	}
	
}
$ari->t->assign("perspectives", $return );

// display
 $ari->t->display($ari->module->admintpldir(). "/perspectiveslist.tpl"); 
 
?>
 
