<?php
# Nutus [©2007 - Nutus, Todos los derechos reservados]
/*
 * Created on 22/06/2007
 * @author Flavio Robles (flavio.robles@nutus.com.ar)
 */

seguridad::RequireLogin();
//if (seguridad :: isAllowed(seguridad_action :: nameConstructor('delete','user','seguridad') ) && 

global $ari;
$ari->t->caching = false;
$user = $ari->get("user");
$error = false;
$close = false;
$ari->popup = true;
if (isset($_POST['si']))
{
	if ($user->delete())
	{	$close = true;
		$user->logout(false);
	}
	else
	{	
		$close = false;
		$error = true;
		if($errores = $user->error()->getErrors())
		{	foreach ($errores as $e)
			{	 $ari->t->assign($e, true );
			}
		}
	}
}
$ari->t->assign("error", $error);
$ari->t->assign("close", $close);
$ari->t->assign("userName", $user->name());
$ari->t->display ($ari->module->usertpldir() . DIRECTORY_SEPARATOR . 'delete.tpl');

?>