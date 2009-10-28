<?
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
######################################## 
*/

// LOGIN BLOCK

global $ari;
$plantilla = $ari->newTemplate();
$plantilla->caching = 0; 

$modulo = new oob_module ('seguridad');

if ($ari->user)
{	
	$plantilla->assign ("logued", true);
	$plantilla->assign ("uname", $ari->user->get('uname'));

}
else
	$plantilla->assign ("logued", false);
	
$plantilla->display($modulo->usertpldir(). "/bl_login.tpl");
?>