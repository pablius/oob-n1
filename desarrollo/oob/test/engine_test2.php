<?php
#OOB/N1 Framework [©2004,2005 - Nutus]
/*
 * Created on 04/02/2005
  @author Pablo Micolini
  @license BSD
 */

require_once ("..\engine.php");
try {

$GLOBALS['ari'] = new OOB_ari ();

$ari->generateOutput();

$time = $ari->finCronometro();
print "Ejecutado en : " . $time . " mS <br> Estas utlizando OOB " . $ari->version(). "<br>";

} catch (OOB_exception $e) {
	
echo "Una Excepcion <br>System: ", $e->getMessage(),"<br> UserMessage: " ,$e->getUserMessage();	  
}
// switch ($var) {
// case: "user"	
//  $this->mode =  "user";
//  break;
//  
// case: "admin"
//   $this->mode =  "admin";
//  break;
//
// case: "popup"  
//    $this->mode =  "popup";
//  break;
// }

?>
