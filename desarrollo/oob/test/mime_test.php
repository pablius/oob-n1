<?php
/*
 * Created on 02/02/2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 include ("..\oob_mimetype.php");
 
 print oob_mimetype::getType("a.xml");
 
 if (oob_mimetype::getType("a.xml") == "text/xml")
 print "<br>test OK";
?>
