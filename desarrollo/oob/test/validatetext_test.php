<?php
/*
 * Created on 03/02/2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
  include ("..\oob_validatetext.php");
  


$numero = "1.12";
$entero = "5";
$email = "pablo@neural.net";
$largo = "123456789";
$corto = "123";


if (!oob_validatetext::isNumeric ($numero)) 
echo "NUMERIC NO";
else echo "NUMERIC OK<br>";
echo "<br>";

if (!oob_validatetext::isInt ($entero)) 
echo "INT NO INT";
else echo "INT OK<br>";
echo "<br>";

if (!oob_validatetext::isPassword ($corto)) 
echo "NO CLAVE";
else echo "SI CLAVE<br>";
echo "<br>";

if (!oob_validatetext::isPassword ($largo)) 
echo "NO CLAVE";
else echo "SI CLAVE<br>";
echo "<br>";

?>
