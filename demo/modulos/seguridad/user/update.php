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
seguridad::RequireLogin();

$ari->t->caching = false;

$usuario = $ari->user;
$ari->t->assign("user", $usuario);

 // no butto get, standard action
  if (!isset ($_POST['update']))
 {
   $ari->t->assign("error", false );
   $ari->t->assign("uname", $usuario->get("uname") );
   $ari->t->assign("id", $usuario->id() );
   $ari->t->assign("email", $usuario->get("email") );
   
 
  $ari->t->display($ari->module->usertpldir(). "/update.tpl");
 
 }else {
 	
 	
 // $usuario->set ('uname', $_POST['uname']);
 
 	if ($_POST['pass'] != "" && $_POST['passtwo'] != "")
 	{$usuario->set ('password', $_POST['pass']);}
 
 $usuario->set ('email', $_POST['email']);
 

// stores?
if ($_POST['pass'] === $_POST['passtwo'])
	{
	if ($usuario->store()) 
		{ 
		header( "Location: " . $ari->get("webaddress") . '/'); 
  		exit;
  		}
	}

// no se pudo grabar, hay un error!
  $ari->t->assign("error", true );
  
 $errores = $usuario->error()->getErrors();
  
   if ($_POST['pass'] !== $_POST['passtwo'])
   $errores[] = "NO_CONCUERDAN";
  
 foreach ($errores as $error)
	 $ari->t->assign($error, true );
  

   $ari->t->assign("uname", $_POST['uname'] );
   $ari->t->assign("id", $_POST['id'] );

  
  if (!in_array ("INVALID_EMAIL", $errores))
   $ari->t->assign("email", $_POST['email'] );

   
   $ari->t->display($ari->module->usertpldir(). "/update.tpl");
 }


 
?>
