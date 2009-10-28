<?
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
######################################## 
*/

global $ari;
$handle = $ari->url->getVars();
$ari->t->caching = 0;

if (isset($handle[1]) && $handle[1] == 'update')
{$ari->t->assign('first',false);}
else
{$ari->t->assign('first',true);}

if (isset($handle[2]) && OOB_validatetext::isClean($handle[2]))
{$ari->t->assign('code',$handle[2]);}

if (isset($_POST['recover']))
{
	$ari->t->assign('posted',true);
	$usuario = oob_user::lostPass($_POST['email']);
	if ($usuario == false)
	{$ari->t->assign('first',true);
	$ari->t->assign('error',true);}
	else
	{
		$from_address = $ari->config->get('email', 'main');
		$from_name = $ari->config->get('name', 'main');
		$plantilla = $ari->newTemplate();
		$plantilla->caching = 0;
		$plantilla->assign ("uname", $usuario[0]->get('uname'));
		$plantilla->assign ("code", $usuario[1]);
		$plantilla->assign ("email", $usuario[0]->get('email'));
		
		$ahora = ob_get_clean();
		ob_start ();
		$plantilla->display($ari->module->usertpldir(). "/forgot-mail.tpl");
		$mensaje = ob_get_clean();
		ob_start ();
		print $ahora;
		
		//////////// mail send
	require_once ($ari->get('enginedir').DIRECTORY_SEPARATOR .'librerias'.DIRECTORY_SEPARATOR.'mimemessage'.DIRECTORY_SEPARATOR.'smtp.php');
	require_once ($ari->get('enginedir').DIRECTORY_SEPARATOR .'librerias'.DIRECTORY_SEPARATOR.'mimemessage'.DIRECTORY_SEPARATOR.'email_message.php');
	require_once ($ari->get('enginedir').DIRECTORY_SEPARATOR .'librerias'.DIRECTORY_SEPARATOR.'mimemessage'.DIRECTORY_SEPARATOR.'smtp_message.php');
	$email_message=new smtp_message_class;
	$email_message->localhost="";
	$email_message->smtp_host=$ari->config->get('delivery', 'main');
	$email_message->smtp_direct_delivery=0;
	$email_message->smtp_exclude_address="";
	$email_message->smtp_user="";
	$email_message->smtp_realm="";
	$email_message->smtp_workstation="";
	$email_message->smtp_password="";
	$email_message->smtp_pop3_auth_host="";
	$email_message->smtp_debug=0;
	$email_message->smtp_html_debug=1;

	$email_message->SetEncodedEmailHeader("To",$usuario[0]->get('email'),$usuario[0]->get('email'));
	$email_message->SetEncodedEmailHeader("From",$from_address,$from_name);
	$email_message->SetEncodedHeader("Subject",$from_name);
	$email_message->AddQuotedPrintableHTMLPart($mensaje);
	$email_message->Send();
		/////////// end mail send

	}
}

if (isset($_POST['update']))
{
	
	$error = array();
	if (!isset($_POST['code']) || $_POST['code'] == null)
	$error[]='INVALID_CODE';
	
	if (!isset($_POST['email']) || $_POST['email'] == null)
	$error[]='INVALID_EMAIL';
	
	if (!isset($_POST['pass']) || !isset($_POST['passtwo']) || $_POST['pass'] == null || $_POST['passtwo'] == null)
	{$error[]='INVALID_PASSWORD';}
	else
	{
	if ($_POST['pass'] !== $_POST['passtwo'])
	$error[]='INVALID_PASSWORD_MATCH';
	}
	
	if (count($error) == 0)
	{
	 if (oob_user::validateLost($_POST['code'],$_POST['email'],$_POST['pass']))
		{
		header( "Location: " . $ari->get('webaddress') . '/seguridad/restored');
 		exit;
		}
		else
		{
		$ari->t->assign('error', true);
		$ari->t->assign('INVALID_CODE', true);
		$ari->t->assign ('email', htmlentities($_POST['email'],0,'UTF-8'));
		}
	} else
	{
		$ari->t->assign('error', true);
	foreach ($error as $mal)
		{	$ari->t->assign($mal, true);	}
	}
	


}

$ari->t->display($ari->module->usertpldir(). "/forgot.tpl");
?>