<?php
#OOB/N1 Framework [�2004,2005 - Nutus]
/*
 * Created on 22-jun-2005
 * @author Victor Stornati (victor.stornati@nutus.com.ar)
 */
 
 if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('config','config','admin')) )
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
}
 
 global $ari;
 
 
 $ari->t->caching = 0; // dynamic content 

  
 $filename = $ari->get('enginedir') .  DIRECTORY_SEPARATOR . 'configuracion' . DIRECTORY_SEPARATOR. 'base.ini.php';
 
 //@todo: falta captar el posible error de una apertura
 
 $config = @file_get_contents($filename);
 
 $update_view = true;
 
	 // check the delete selector, and delete if selected
	if (isset ($_POST['delete_button']) )
	{
		$_POST['restore_view'] = true;
		if (isset($_POST['selected_files']))
		{
			foreach($_POST['selected_files'] as $file)
			{
				$result = @unlink($ari->get('enginedir') .  DIRECTORY_SEPARATOR . 'configuracion'. DIRECTORY_SEPARATOR. $file );	
			}
		}
		
	}
	
	// check the restore selector
	if (isset ($_POST['restore_button']) )
	{
		if (isset($_POST['opt_restore']))
		{
			$filename = $ari->get('enginedir') .  DIRECTORY_SEPARATOR . 'configuracion' . DIRECTORY_SEPARATOR. $_POST['opt_restore'];
 			$config = @file_get_contents($filename);
		}
		
	}
	 
 if (isset($_POST['update_button']) && isset($_POST['config_string']) )
 {   
	if (isset($_POST['backup_check']))
	{
	 	$filename_back = $ari->get('enginedir') . DIRECTORY_SEPARATOR . 'configuracion'. DIRECTORY_SEPARATOR. 'base.ini.php.' . date('Ymd_His') . '.bak';
	 	//@todo: falta captar el posible error de una mala escritura 
	 	@file_put_contents ( $filename_back, $config);
	}
	
	@file_put_contents ( $filename, $_POST['config_string']);
	
	$config = @file_get_contents($filename);
 } 

 if (isset($_POST['restore_view']) )
 {   
	$update_view = false;
	$lista = listBackups();
	$ari->t->assign("files", $lista );
 }
 
 $ari->t->assign("config",$config);
 $ari->t->assign("filename",$filename);
 $ari->t->assign("update_view",$update_view);

 $ari->t->display($ari->module->admintpldir(). "/editconfig.tpl");
   
 function listBackups ()
 {
 	global $ari;

 	// iterate throught "modulos" dir to see whats available.
 	$path = $ari->get('enginedir') .  DIRECTORY_SEPARATOR . 'configuracion';
  	if ($handle = opendir($path)) 
  	{
   		$i = 0;
   		
   		while (false !== ($file = readdir($handle)) )
   		{ 
        	if (filetype ( $path . DIRECTORY_SEPARATOR . $file) == 'file' && eregi("(base.ini.php.)([0-9]{8})(_)([0-9]{6})(.bak)", $file, $split) )
      		{	
      			$contents = array();
      			$availables[$i]['date'] = substr($split[2],0,4) . "-" . substr($split[2],4,2) . "-" . substr($split[2],6,2);
      			$availables[$i]['date'] .=  " " . substr($split[4],0,2) . ":" . substr($split[4],2,2) . ":" . substr($split[4],4,2);
      			$availables[$i]['name'] = $file;
      			$availables[$i]['path'] = $path . DIRECTORY_SEPARATOR . $file;
      			$i++;
      		}
       	} 
   	}
   closedir($handle); 
   return $availables;
 }
 
 
?>
