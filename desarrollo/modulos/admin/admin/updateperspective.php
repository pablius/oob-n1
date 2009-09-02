<?php
#OOB/N1 Framework [�2004,2005 - Nutus]
/*
 * Created on 17-jun-2005
 * @author Victor Stornati (victor.stornati@nutus.com.ar)
*/
  

global $ari;

$handle = $ari->url->getVars();
$ari->t->caching = false;

if (isset ($handle[3]))
{ 
	//todo: falta validacion de handle[1]
	//if (OOB_validatetext :: isNumeric ($handle[1]) )
	//{
		$perspective = new oob_perspective ($handle[3]);
		$ari->t->assign("name_perspective",$perspective->name() );
	//}
	
}
else
{
 	throw new OOB_exception("INVALID_PERSPECTIVE", "501", "INVALID_PERSPECTIVE", false);	
}

$ari->t->assign("new_name", "" );
 
$ari->t->assign("new_name", $perspective->name() );
   
if (isset ($_POST['name']))
{
	$ari->t->assign("new_name", $_POST['name'] );
}
	
//Adds the selected roles
if(isset ($_POST['AddRole']) && isset($_POST['roles_select']) )
{
	foreach($_POST['roles_select'] as $id_role)
  	{
  		$perspective->addRole($id_role);
  	}
}// end if isset
	
//Removes the selected roles
if(isset ($_POST['DelRole']) && isset($_POST['roles_members_select']) )
{	
	foreach($_POST['roles_members_select'] as $id_role)
	{
  		$perspective->removeRole($id_role);
  	}
}// end if isset
	
//list roles members
if($roles_miembros = OOB_perspective::listRolesFor($perspective->name()))
{
	$i = 0;
	$return = array();
	foreach ($roles_miembros as $m)
	{
		$m = new seguridad_role($m);
		$return[$i]['id']= $m->get('role');
		$return[$i]['name']= $m->get('name');
		++$i;
	}
	$ari->t->assign("roles_miembros", $return );
}//end if
	
//search roles no members
if($roles = oob_perspective::searchNoMembers("",DELETED,OPERATOR_DISTINCT, $perspective->name(),ROLE))
{
	$i = 0;
	$return = array();
	foreach ($roles as $r)
	{
		$r = new seguridad_role($r);
		$return[$i]['id']= $r->get('role');
		$return[$i]['name']= $r->get('name');
		++$i;
	}
	$ari->t->assign("roles", $return );
}//end if
	
//Adds the selected modules
if(isset ($_POST['AddModule']) && isset($_POST['modules_select']) )
{
	foreach($_POST['modules_select'] as $name_module)
  	{
  		$perspective->addModule($name_module);
  	}
}// end if isset
	
//Removes the selected modules
if(isset ($_POST['DelModule']) && isset($_POST['modules_members_select']) )
{	
	foreach($_POST['modules_members_select'] as $name_module)
  	{
  		$perspective->removeModule($name_module);
  	}
}// end if isset
	
//list modules members
if($modules_miembros = OOB_perspective::listModulesFor($perspective->name()))
{
	$i = 0;
	$return = array();
	foreach ($modules_miembros as $m)
	{
		$m = new oob_module($m);
		$return[$i]['id']= $m->name();
		$return[$i]['name']= $m->nicename();
		++$i;
	}
	$ari->t->assign("modules_miembros", $return );
}//end if
	
//search modules no members
if($modules = oob_perspective::searchNoMembers("",DELETED,OPERATOR_DISTINCT, $perspective->name(),MODULE))
{
	$i = 0;
	$return = array();
	foreach ($modules as $m)
	{
		$m = new oob_module($m);
		$return[$i]['id']= $m->name();
		$return[$i]['name']= $m->nicename();
		++$i;
	}
	$ari->t->assign("modules", $return );
}//end if
	
//display			
$ari->t->display($ari->module->admintpldir(). "/updateperspective.tpl");
 
?>

