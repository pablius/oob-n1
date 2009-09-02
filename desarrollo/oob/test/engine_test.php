<?php
#OOB/N1 Framework [©2004,2005 - Nutus]
/*
 * Created on 04/02/2005
  @author Pablo Micolini
  @license BSD
 */

require_once ("..\engine.php");
try {

$ari = new OOB_ari ();


if ($a = $ari->get('agent')->getLang())
print $a . "<br>";
else
print "no hay var<br>";
// usleep(54200);

$_SESSION["test"]++;
print $_SESSION["test"];

$ari->t->assign("Name","Fred Irving Johnathan Bradley Peppergill");
$ari->t->assign("FirstName",array("John","Mary","James","Henry"));
$ari->t->assign("LastName",array("Doe","Smith","Johnson","Case"));
$ari->t->assign("Class",array(array("A","B","C","D"), array("E", "F", "G", "H"),
	  array("I", "J", "K", "L"), array("M", "N", "O", "P")));

$ari->t->assign("contacts", array(array("phone" => "1", "fax" => "2", "cell" => "3"),
	  array("phone" => "555-4444", "fax" => "555-3333", "cell" => "760-1234")));

$ari->t->assign("option_values", array("NY","NE","KS","IA","OK","TX"));
$ari->t->assign("option_output", array("New York","Nebraska","Kansas","Iowa","Oklahoma","Texas"));
$ari->t->assign("option_selected", "NE");
$ari->t->assign("bold", false);
$ari->t->assign("title", "n");
$ari->t->display('oob/librerias/smarty/test/index.tpl');

$time = $ari->finCronometro();
print "Ejecutado en : " . $time . " mSegundos <br> Estas utlizando OOB " . $ari->version(). "<br>";

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
