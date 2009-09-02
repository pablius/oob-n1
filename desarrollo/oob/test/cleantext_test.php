<?php
/*
 * Created on 03/02/2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
  include ("..\oob_cleantext.php");
 	$allowed_tags = array('<br>','<i>');
$ct = new OOB_cleantext($allowed_tags);
 	$text = '<a href="http://www.yahoo.com">http://www.yahoo.com/</a>'
 			.'<br><b>Yahoo Server</b><br><i>Search Engine</i>';
 
 	echo $ct->dropHTML($text) . "<br><br><br>";
 		echo $ct->dropHTML($text, '<a>,<br>,<i>') . "<br><br><br>";
 	echo $ct->shortText($text,10) . "<br>";		


?>
