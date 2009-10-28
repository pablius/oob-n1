<?php
#OOB/N1 Framework [©2004,2005 - Nutus]
/*
 * Created on 11/05/2005
 * @author Pablo Micolini
 */

// get modules

if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('modules','config','admin')) )
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
}

global $ari;
$handle = $ari->url->getVars();
$ari->t->caching = 0; // dynamic content 

// check the selector, and redirects if selected
if (isset ($_POST['kind_submit']) && in_array($_POST['kind_select'], OOB_module::getViews()) &&  in_array($_POST['kind_order'], OOB_module::getOrders()))
 {header( "Location: " . $ari->get("adminaddress") . '/admin/config/modules/' . $_POST['kind_select'] . '/' .$_POST['kind_order']);}

// check the modules update status selector, and update if selected
if (isset ($_POST['select_submit']) )
{
 	foreach($_POST['hidden_name'] as $moduleName)
 	{
 		$array = $_POST['opt_modules'];
		$module = new OOB_module($moduleName);
		switch($array[$moduleName])
		{
			case 0:
			{
				$module->disable();
				break;
			}
			case 1:
			{
				$module->enable();
				break;
			}	
		}
  	}
}

// check the modules update list
if (isset ($_POST['update_modules_list']) )
{
	$module_object = new OOB_module(); 
	$module_object->updateModulesList ();
	header( "Location: " . $ari->get("adminaddress") . '/admin/config/modules/');
}

// set the get method
if (!isset($handle[2]) || (!in_array($handle[2], OOB_module::getViews())))
{$handle[2] = "all";}

$ari->t->assign("view", $handle[2] );

// set the order
if (!isset($handle[3]) || (!in_array($handle[3], OOB_module::getOrders())))
{$handle[3] = "nicename";}
$ari->t->assign("order",$handle[3]);

//selectors data
$ari->t->assign("kind_values",OOB_module::getViews());
$ari->t->assign("kind_names",OOB_module::getViews());
//$ari->t->assign("change_values",OOB_module::getViews());


// @todo set the amount so we know the "page", need a page drawer :(

// finally get the data
if ($modules = OOB_module::listModules($handle[2], true, $handle[3] )) {

// show time
$i = 0;

foreach ($modules as $m)
{
$return[$i]['nicename']= $m->nicename();
$return[$i]['modulename']= $m->name();
$return[$i]['description']= $m->description();

if($m->isenabled())
{
	$return[$i]['checked_yes']= "checked";
	$return[$i]['checked_no']= "";		
}
else
{
	
	$return[$i]['checked_yes']= "";
	$return[$i]['checked_no']= "checked";		
}

if(!$m->primary())
{
	$return[$i]['disabled']= "disabled";		
}
else
{
	$return[$i]['disabled']= "";		
}

++$i;
}
$ari->t->assign("modules", $return );
}
// display
 $ari->t->display($ari->module->admintpldir(). "/moduleslist.tpl");
?>
