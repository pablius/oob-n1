<?php
#OOB/N1 Framework [�2004,2005 - Nutus]
/*
 * Created on 22-jun-2005
 * @author Victor Stornati (victor.stornati@nutus.com.ar)
 */
global $ari;
$ari->t->caching = 0; // dynamic content 

 if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('cache','config','admin')) )
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
}

 	$delete = false;
 	
	if (isset($_POST['delete_button']))
	{			
 		$delete = true;

 		$ari->clearCache();
 	} 
 	 $ari->t->assign("delete",$delete);
 	//display
 	$ari->t->display($ari->module->admintpldir(). "/cache.tpl");
?>
