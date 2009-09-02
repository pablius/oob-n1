<?php
#OOB/N1 Framework [�2004,2005 - Nutus]
/*
 * Created on 26/02/2005
 * @author Pablo Micolini
 */
 /////-----------
require_once ("../engine.php");
require_once ("../oob_user.php"); 

$GLOBALS['ari']= new OOB_ari();

//$u= new oob_user(1);
//
//if ($u->get("uname"))
//	print $u->get("uname");
//else
//	print "fail";
//print $u->get("status");
//print "<hr>";
$auuu = oob_user::login ("a", "pablo");
if (!$auuu)
{
print "no user<hr>";
print md5 ("juan");
}
// $auuu->set ('email',"yo@pablius.com.ar");
	//$auuu->logout();
//$auuu = oob_user::islogued();
//print "returns:". $auuu;
// 
//print "<hr>";
if (is_a($auuu, 'OOB_user'))
{
//
	print $auuu->get("email");
	$auuu->set ('email','pablo@compuar.com');
$auuu->set ('status','a');
}


//$auuu->set ("uname","pablo");
//$auuu->set ('password','pablo');

if ($auuu->store())
print $auuu->get("email");
else
{
	$error =  $ari->error->geterrorsfor("oob_user");
print "<hr>";
foreach ($error as $er)
{
print "<br>-". $er;
}
}

?>
