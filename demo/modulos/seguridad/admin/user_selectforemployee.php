<?php
/* Nutus, Todos los derechos reservados 2005
 * Creado: 28-nov-2005
 *
 */
 
if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('selectforemployee','user','seguridad')) )
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
} 

global $ari;
$handle = $ari->url->getVars();
$ari->t->caching = 0;
$ari->popup = true;

// valida pos
$pos = 0;
if (isset($_GET['pos']) && OOB_validatetext::isNumeric($_GET['pos']) && $_GET['pos'] > 0)
{	$pos = $_GET['pos'];
}
	
//levanta el limit
$modulo = new oob_module ("personnel");
$limit = $modulo->config()->get('limit', 'employee');
$ari->t->assign ('limit', $limit);
$ari->t->assign ('total', oob_user::userCountNoAsigned());

$users = array();
if ($return = oob_user :: listNoAssigned('uname', $pos, $limit) ) 
{
	// show time
	$i = 0;
	foreach ($return as $u)
	{
		$users[$i]['id']= $u->get('user');
		$users[$i]['uname']= $u->name();
		$users[$i]['unameClean']= OOB_validatetext::cleanToScript($u->name());
		$users[$i]['email']= $u->get('email');
		$users[$i]['status']= oob_user::getStatus($u->get('status'));
		++$i;
	}
}//end if
$ari->t->assign("users", $users );

// display
$ari->t->display($ari->module->admintpldir(). "/user_selectforemployee.tpl");
 
?>
