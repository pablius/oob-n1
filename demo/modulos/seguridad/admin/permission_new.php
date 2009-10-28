<?php
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
######################################## 
*/

OOB_module :: includeClass('seguridad','seguridad_permission'); 
 
if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('new','permission','seguridad')) )
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
} 
 
global $ari;
$ari->t->assign("form", true);
$sp = new oob_safepost ("form");

$modSelect = '';
$arrModulo = array();
$arrIdModulo = array();
if ($objModulo =  OOB_module :: listModules())
{	foreach($objModulo as $m)
	{	$arrModulo []= $m->nicename();
		$arrIdModulo[]=$m->name();
	
		if(isset($_POST['cboModulo']))
		{	//echo $_POST['cboModulo']."<br>";
			if($_POST['cboModulo'] == $m->name())
			{	$modSelect = $m->name();
			}
		}
	}
}

$ari->t->assign("arrModulo", $arrModulo);
$ari->t->assign("arrIdModulo", $arrIdModulo);
$ari->t->assign("modSelect", $modSelect);


if (!isset ($_POST['guardar']))
{	
	$ari->t->assign("newName", "");
	$ari->t->assign("newNiceName", "");
} 
else 
{
	//verificar datos enviados duplicados
	if(!$sp->Validar())
	{	$ari->error->addError ('seguridad_permission', 'SENT_DUPLICATE_DATA');
	}

	$permiso = new seguridad_permission();
	
	$permiso->set('modulename', $_POST['cboModulo']);
 	$permiso->set('name', $_POST['txtName']);
 	$permiso->set('nicename', $_POST['txtNiceName']);

	//stores?
	if ($permiso->store()) 
	{	
		header( "Location: " . $ari->get("adminaddress") . '/seguridad/permission/new'); 
  		exit;
	}
	else
	{
		$ari->t->assign("form", true);
		$ari->t->assign("error", true );
	    $errores = $ari->error->getErrorsfor("seguridad_permission");
		foreach ($errores as $error)
	  	{	$ari->t->assign($error, true );
	  	}
	  	
		//refrescar template
	  	$name = OOB_validatetext :: inputHTML($_POST['txtName']);
		$ari->t->assign("newName", $name);

		$niceName = OOB_validatetext :: inputHTML($_POST['txtNiceName']);
		$ari->t->assign("newNiceName", $niceName);
	}
}

$ari->t->assign("formElement", $sp->FormElement());
$ari->t->display($ari->module->admintpldir(). "/permission_new.tpl");

?>