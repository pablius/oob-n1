<?php
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
######################################## 
*/
 global $ari;
 $sp = new oob_safepost ("log_in");

 // check if already loged in
if (is_a($ari->user, 'oob_user'))
{ 	header( "Location: " . $ari->get("adminaddress") . '/');
 	exit;
} 

// no button get, standard action
if (!isset ($_POST['login']) )
{
	header("HTTP/1.1 401 Unauthorized");
	$ari->t->assign("error", false );
	$ari->t->assign('SENT_DUPLICATE_DATA', false);
}
else 
{// login!
	
	//verificar datos enviados duplicados
	if(!$sp->Validar())
	{	$ari->t->assign('error', true);
		$ari->t->assign('SENT_DUPLICATE_DATA', true);
	}	
	else
	{
		if (oob_user::login ($_POST['uname'], $_POST['pass']))
		{
			if (isset ($_SESSION['redirecting']))
			{
				$dirijidme = $_SESSION['redirecting'];
				unset ($_SESSION['redirecting']);
				header( "Location: " . $ari->get("adminaddress") . $dirijidme );
			}
			else
				header( "Location: " . $ari->get("adminaddress") . '/');
			
			exit;
		}
		else
		{
			$ari->t->assign("error", true );
		}
		
	}
}


$ari->t->assign("formElement", $sp->FormElement());
$ari->t->display($ari->module->admintpldir(). "/login.tpl");


?>
