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
 $default_login = $ari->get('config')->get('defaultlogin', 'main');
 
 // check if already loged in
 if (is_a($ari->user, 'oob_user'))
 { if (isset ($_SESSION['redirecting']))
 	{
 		  		$dire = $_SESSION['redirecting'];
  				unset ($_SESSION['redirecting']);
  				header( "Location: " . $ari->get("webaddress") . $dire  ); 
 	}
 	else
 	header( "Location: " . $ari->get('webaddress') . $default_login);
 exit;
 } 
 
 //see if a user can register from user-interface
 $allowregister = false;
 

 
 $ar_config = $ari->get('config')->get('can-self-register', 'user');
 
  if ( $ar_config === "true" || $ar_config === "yes")
  	$allowregister = true;
 
 // no butto get, standard action
  if (!isset ($_POST['login']) && !isset ($_POST['forgot']) && !isset ($_POST['register']) )
 {
 $ari->t->assign("register", $allowregister );
  $ari->t->assign("login", true );
  $ari->t->assign("error", false );
  $ari->t->assign("newname", '' );
  $ari->t->assign("newemail", false );
 $ari->t->display($ari->module->usertpldir(). "/login.tpl");
 }
 
 //login 
 if (isset ($_POST['login']))
 {
 if (oob_user::login ($_POST['uname'], $_POST['pass']))
  { if (isset ($_SESSION['redirecting']))
  	{
  		$dire = $_SESSION['redirecting'];
  		unset ($_SESSION['redirecting']);
  		header( "Location: " . $ari->get("webaddress") . $dire  ); }
  	else
  	{
  		header( "Location: " . $ari->get("webaddress") . $default_login ); 
  	}
  exit;
  }
 else
 {
 $ari->t->assign("register", false );
   $ari->t->assign("login", true );
  $ari->t->assign("error", true );
 $ari->t->display($ari->module->usertpldir(). "/login.tpl");
 }
 }
 
 //forgot pass
  if (isset ($_POST['forgot']))
 {
 	header( "Location: " . $ari->get("webaddress") . '/seguridad/forgot'); 
 }
?>
