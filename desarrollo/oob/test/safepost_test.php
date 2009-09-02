<?php
/*
 * Created on 03/02/2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
  include ("..\oob_safepost.php");
  
  $sp = new oob_safepost ("casita");


session_start();

$message = $input = '';

if (isset($_POST['testform_button'])) { 
	$message = (string) ($sp->Validar() == TRUE) ? 'Input OK' : 'Data sent twice';
	$input = $_POST['testform_button'];
} 

?>
<html>
	<head>
	<title>Sample</title>
	</head>
		<body>
		<p><b><?php echo $message; ?></b></p>
		<p><?php echo $input; ?></p>
	<form name="token" action="safepost_test.php" method="post">
	  	<?php
	  	/* automatically sets new token after old token was checked with first
	  	output of form element */
	  	echo $sp->FormElement();
	  	?>
		<input name="teststring" type="text" value=""><br>
		<input type="submit" value="Send" name="testform_button">
		</form>
  </body>
</html>
