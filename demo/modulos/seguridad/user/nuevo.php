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
 {header( "Location: " . $ari->get("webaddress") . '/');
 exit;
 } 
 
 //see if a user can register from user-interface
 $allowregister = false;
 $ar_config = $ari->get("config")->get('can-self-register', 'user');
  if ( $ar_config === "true" || $ar_config === "yes")
 $allowregister = true;
 
 // no butto get, standard action
  if (!isset ($_POST['register']))
 {
 $ari->t->assign("register", $allowregister );
  $ari->t->assign("error", false );
   $ari->t->display($ari->module->usertpldir(). "/login.tpl");
 } 
 
 //nuevo 
 if (isset ($_POST['register']))
 {
 $usuario = new oob_user();
 $usuario->set ('uname', $_POST['uname']);
 $usuario->set ('password', $_POST['pass']);
 $usuario->set ('email', $_POST['email']);
 
 $validation = $usuario->get('new_validation');

if ($validation == "no")
  $usuario->set ('status', "1");
else
  $usuario->set ('status', "0");

  if (!isset ($_POST['condiciones']) || $_POST['condiciones'] !='checkbox')
   		$usuario->error()->addError ( "INVALID_condiciones");

if (isset($_POST['pass']) && isset($_POST['passtwo']) && $_POST['pass'] === $_POST['passtwo'])
	{
	if ($usuario->store()) 
		{  
			$usuario->linkStandardGroup();
		oob_user::login($_POST['uname'],$_POST['pass'] );
		if (isset ($_SESSION['redirecting']))
		{
			$dire = $_SESSION['redirecting'];
			unset ($_SESSION['redirecting']);
			header( "Location: " . $ari->get("webaddress") . $dire  ); 		
		}
		else
		{
			header( "Location: " . $ari->get("webaddress") . $default_login);
		}
  		exit;
  		}
	}

// muestra los errores, sin la parte de login ;) (y los valores llenos de lo demas)
 $ari->t->assign("register", true );
  $ari->t->assign("login", false );
  $ari->t->assign("error", true );
  $errores = $usuario->error()->getErrors();
  
 if (!isset($_POST['pass']) || !isset($_POST['passtwo']) || $_POST['pass'] !== $_POST['passtwo'])
   $errores[] = "NO_CONCUERDAN";
   
  foreach ($errores as $error)
  $ari->t->assign($error, true );
  
 if (!in_array ("INVALID_USER", $errores))
   $ari->t->assign("newname", $_POST['uname'] );
   
  if (!in_array ("INVALID_EMAIL", $errores) && !in_array ("INVALID_USER", $errores))
   $ari->t->assign("newemail", $_POST['email'] );

   
  $ari->t->display($ari->module->usertpldir(). "/login.tpl");
 }

?>
